<?php
session_start();
require_once '../../config/db.php';
require_once '../../lib/gestor_pacientes.php'; // Para obtener datos del paciente

// --- Validación de Acceso ---
if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

// --- Obtención del ID del Paciente ---
$id_paciente = isset($_GET['id_paciente']) ? intval($_GET['id_paciente']) : 0;
if ($id_paciente === 0) {
    header("Location: pacientes_vinculados.php?id_usr=" . $_SESSION['id_usr'] . "&error=Paciente no especificado");
    exit;
}

// --- Verificación de Vinculación ---
// Un cuidador solo puede ver el historial de un paciente si está vinculado a él.
$id_cuidador = $_SESSION['id_usr'];
$sql_check = "SELECT 1 FROM cuidador_paciente WHERE id_cuidador = ? AND id_paciente = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, 'ii', $id_cuidador, $id_paciente);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
if (mysqli_num_rows($result_check) === 0) {
    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador . "&error=No tienes permiso para ver este historial");
    exit;
}

// --- Obtención de Datos del Paciente ---
$paciente = obtener_paciente_por_id($conn, $id_paciente);
if (!$paciente) {
    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador . "&error=Paciente no encontrado");
    exit;
}

// --- Obtención de Datos para el Historial ---

// Última cita
$sql_cita = "SELECT * FROM cita WHERE id_paciente = ? ORDER BY fecha DESC, hora DESC LIMIT 1";
$stmt_cita = mysqli_prepare($conn, $sql_cita);
mysqli_stmt_bind_param($stmt_cita, 'i', $id_paciente);
mysqli_stmt_execute($stmt_cita);
$res_cita = mysqli_stmt_get_result($stmt_cita);
$ultima_cita = mysqli_fetch_assoc($res_cita);

// Último medicamento
$sql_med = "SELECT * FROM medicamento WHERE id_paciente = ? ORDER BY id_medicamento DESC LIMIT 1";
$stmt_med = mysqli_prepare($conn, $sql_med);
mysqli_stmt_bind_param($stmt_med, 'i', $id_paciente);
mysqli_stmt_execute($stmt_med);
$res_med = mysqli_stmt_get_result($stmt_med);
$ultimo_med = mysqli_fetch_assoc($res_med);

// Datos de Signos Vitales para la gráfica
$sql_signos = "SELECT tipo_signo, valor, fecha_registro FROM signos_vitales 
               WHERE id_paciente = ? AND fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)
               ORDER BY fecha_registro ASC";
$stmt_signos = mysqli_prepare($conn, $sql_signos);
mysqli_stmt_bind_param($stmt_signos, 'i', $id_paciente);
mysqli_stmt_execute($stmt_signos);
$res_signos = mysqli_stmt_get_result($stmt_signos);

$signos_vitales = [];
while ($row = mysqli_fetch_assoc($res_signos)) {
    $signos_vitales[] = $row;
}

$labels_grafica = [];
$datos_grafica = [];
$tipo_signo_grafica = "No registrado";

if (!empty($signos_vitales)) {
    // Agrupar por tipo de signo para la gráfica
    $ultimo_tipo = end($signos_vitales)['tipo_signo'];
    $tipo_signo_grafica = htmlspecialchars($ultimo_tipo);

    foreach ($signos_vitales as $signo) {
        if ($signo['tipo_signo'] === $ultimo_tipo) {
            $labels_grafica[] = date('d/m', strtotime($signo['fecha_registro']));
            $datos_grafica[] = $signo['valor'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de <?= htmlspecialchars($paciente['nom_paciente']) ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <style>
        .fixed-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgb(158, 234, 193) 0%, rgb(42, 191, 255) 100%);
            z-index: -1;
        }
        .main-container {
            padding-top: 100px;
            padding-bottom: 40px;
        }
        .card-historial {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-clip: border-box;
            border: none;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            transition: none;
        }
        .card-historial:hover {
            transform: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .card-header-custom {
            background-color: var(--green);
            color: white;
            border-bottom: 0;
            border-radius: 15px;
            font-weight: bold;
            padding: 1rem 1.25rem;
        }
        .card-footer-custom {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 15px;
            padding: 0.75rem 1.25rem;
            text-align: right;
        }
        .info-item {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .navbar-icon-text .bi {
            margin-right: 8px;
        }
    </style>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container navbar-container">
            <!-- Logo a la izquierda -->
            <div class="navbar-section navbar-section-logo">
                <a href="../index.php">
                    <img class="navbar-brand logo" src="../assets/img/logo.png" alt="Logo">
                </a>
            </div>

            <!-- Botón de menú para teléfonos -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menú de navegación centrado -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="./index.php">
                            <i class="bi bi-person"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="mis_compras.php">
                            <i class="bi bi-box-seam"></i>
                            <span>Mis compras</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>">
                            <i class="bi bi-person-fill-add"></i>
                            <span>Pacientes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="editarinfo.php?id_usr=<?php echo $_SESSION['id_usr']; ?>">
                            <i class="bi bi-gear"></i>
                            <span>Configuración</span>
                        </a>
                    </li>
                </ul>

                <!-- Botón de inicio de sesión a la derecha -->
                <div class="navbar-section navbar-section-login">
                    <a class="btn-void nav-login-btn navbar-icon-text" href="#">
                        <i class="bi bi-person-circle"></i>
                        <span><?php echo isset($_SESSION['nom_usr']) ? $_SESSION['nom_usr'] : 'Usuario'; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</head>
<body>
    <div class="fixed-background"></div>

    <div class="container main-container">
        <div class="text-center mb-4">
            <h1>Historial</h1>
            <h2 ><?= htmlspecialchars($paciente['nom_paciente']) ?></h2>
        </div>

        <!-- Gráfica de Signos Vitales -->
        <div class="card-historial">
            <div class="card-header card-header-custom">
                Gráficos (<?= $tipo_signo_grafica ?> en los últimos 7 días) 
            </div>
            <div class="card-body">
                <?php if (!empty($datos_grafica)): ?>
                    <canvas id="vitalSignsChart" style="max-height: 350px;"></canvas>
                <?php else: ?>
                    <p class="text-center text-muted py-5">No hay registros de signos vitales en la última semana.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer-custom">
                <a href="historial_signos_vitales.php?id_paciente=<?= $id_paciente ?>" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
        </div>

        <div class="row">
            <!-- Tarjeta de Cita -->
            <div class="col-md-6 d-flex">
                <div class="card-historial h-100 w-100">
                    <div class="card-header card-header-custom">
                        Citas: última cita registrada
                    </div>
                    <div class="card-body">
                        <?php if ($ultima_cita): ?>
                            <p class="info-item"><span class="info-label">Motivo:</span> <?= htmlspecialchars($ultima_cita['titulo']) ?></p>
                            <p class="info-item"><span class="info-label">Fecha:</span> <?= date('d/m/Y', strtotime($ultima_cita['fecha'])) ?></p>
                            <p class="info-item"><span class="info-label">Hora:</span> <?= htmlspecialchars($ultima_cita['hora']) ?></p>
                            <p class="info-item"><span class="info-label">Descripción:</span> <?= htmlspecialchars($ultima_cita['descripcion']) ?></p>
                        <?php else: ?>
                            <p class="text-muted">No hay citas registradas.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer-custom">
                        <a href="historial_citas.php?id_paciente=<?= $id_paciente ?>" class="btn btn-primary">Ver citas</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Medicamento -->
            <div class="col-md-6 d-flex">
                <div class="card-historial h-100 w-100">
                    <div class="card-header card-header-custom">
                        Medicamentos: último Medicamento preescrito
                    </div>
                    <div class="card-body">
                        <?php if ($ultimo_med): ?>
                            <p class="info-item"><span class="info-label">Nombre:</span> <?= htmlspecialchars($ultimo_med['nombre']) ?></p>
                            <p class="info-item"><span class="info-label">Dosis:</span> <?= htmlspecialchars($ultimo_med['dosis']) ?></p>
                            <p class="info-item"><span class="info-label">Frecuencia:</span> Cada <?= htmlspecialchars($ultimo_med['frecuencia_horas']) ?> horas</p>
                        <?php else: ?>
                            <p class="text-muted">No hay medicamentos registrados.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer-custom">
                        <a href="historial_medicamentos.php?id_paciente=<?= $id_paciente ?>" class="btn btn-primary btn-sm">Ver medicamentos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    <?php if (!empty($datos_grafica)): ?>
        const ctx = document.getElementById('vitalSignsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels_grafica) ?>,
                datasets: [{
                    label: '<?= $tipo_signo_grafica ?>',
                    data: <?= json_encode($datos_grafica) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    <?php endif; ?>
    </script>
</body>
</html>
