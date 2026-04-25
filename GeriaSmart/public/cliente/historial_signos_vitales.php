<?php
session_start();
require_once '../../config/db.php';
require_once '../../lib/gestor_pacientes.php';

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

// --- Obtención y Agrupación de TODOS los Signos Vitales ---
$sql_signos = "SELECT tipo_signo, valor, fecha_registro FROM signos_vitales 
               WHERE id_paciente = ?
               ORDER BY tipo_signo, fecha_registro ASC";
$stmt_signos = mysqli_prepare($conn, $sql_signos);
mysqli_stmt_bind_param($stmt_signos, 'i', $id_paciente);
mysqli_stmt_execute($stmt_signos);
$res_signos = mysqli_stmt_get_result($stmt_signos);

$signos_por_tipo = [];
while ($row = mysqli_fetch_assoc($res_signos)) {
    $signos_por_tipo[$row['tipo_signo']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signos Vitales de <?= htmlspecialchars($paciente['nom_paciente']) ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .card-header-custom {
            background-color: var(--green);
            color: white;
            border-bottom: 0;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-weight: bold;
            padding: 1rem 1.25rem;
        }
        .navbar-icon-text .bi {
            margin-right: 8px;
        }
        .chart-container {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="fixed-background"></div>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container navbar-container">
            <a href="../index.php" class="navbar-brand">
                <img src="../assets/img/logo.png" alt="Logo GeriaSmart" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="index.php">
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
                <div class="navbar-section navbar-section-login">
                    <a class="btn-void nav-login-btn navbar-icon-text" href="#">
                        <i class="bi bi-person-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['nom_usr']); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="text-center mb-4">
            <h1>Historial Completo de Signos Vitales</h1>
            <h2><?= htmlspecialchars($paciente['nom_paciente']) ?></h2>
            <a href="historial.php?id_paciente=<?= $id_paciente ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Historial Principal
            </a>
        </div>

        <?php if (!empty($signos_por_tipo)): ?>
            <div class="row">
                <?php 
                $chart_index = 0;
                foreach ($signos_por_tipo as $tipo_signo => $registros): 
                    $labels = [];
                    $datos = [];
                    foreach ($registros as $registro) {
                        $labels[] = date('d/m/Y H:i', strtotime($registro['fecha_registro']));
                        $datos[] = $registro['valor'];
                    }
                ?>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card-historial">
                            <div class="card-header card-header-custom">
                                <?= htmlspecialchars($tipo_signo) ?>
                            </div>
                            <div class="chart-container">
                                <canvas id="chart-<?= $chart_index ?>"></canvas>
                            </div>
                        </div>
                    </div>
                <?php 
                    $chart_index++;
                endforeach; 
                ?>
            </div>
        <?php else: ?>
            <div class="card-historial">
                <div class="card-body text-center py-5">
                    <p class="text-muted">No hay ningún registro de signos vitales para este paciente.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($signos_por_tipo)): ?>
            <?php 
            $chart_index = 0;
            foreach ($signos_por_tipo as $tipo_signo => $registros):
                $labels = [];
                $datos = [];
                foreach ($registros as $registro) {
                    $labels[] = date('d/m/Y H:i', strtotime($registro['fecha_registro']));
                    $datos[] = $registro['valor'];
                }
            ?>
                var ctx<?= $chart_index ?> = document.getElementById('chart-<?= $chart_index ?>').getContext('2d');
                new Chart(ctx<?= $chart_index ?>, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($labels) ?>,
                        datasets: [{
                            label: '<?= htmlspecialchars($tipo_signo) ?>',
                            data: <?= json_encode($datos) ?>,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: false 
                            },
                            x: {
                                ticks: {
                                    maxRotation: 90,
                                    minRotation: 45,
                                    autoSkip: true,
                                    maxTicksLimit: 15 
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    }
                });
            <?php 
                $chart_index++;
            endforeach; 
            ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
