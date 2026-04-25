<?php
session_start();
require_once '../../config/db.php';

$error   = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

// Solo usuarios logueados tipo Paciente (2) o Cuidador (3) pueden entrar al área de cliente
if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

// Validar que venga un id_usr en el URL
if (!isset($_GET['id_usr'])) {
    echo "<script>
            alert('ID de usuario no reconocido.');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$id_cuidador = intval($_GET['id_usr']);

// Verificar que solo pueda ver los pacientes vinculados a SU propia cuenta
if (!isset($_SESSION['id_usr']) || $id_cuidador !== intval($_SESSION['id_usr'])) {
    header("Location: index.php?error=Solo puedes ver los pacientes vinculados a tu propia cuenta.");
    exit;
}

// Obtener pacientes vinculados al cuidador logueado
$sql = "SELECT 
            p.id_paciente,
            p.nom_paciente,
            p.mail,
            p.fecha_nacimiento,
            p.genero,
            p.peso,
            p.estatura,
            p.padecimientos,
            cp.fecha_vinculacion
        FROM cuidador_paciente cp
        INNER JOIN paciente p ON cp.id_paciente = p.id_paciente
        WHERE cp.id_cuidador = ?
        ORDER BY p.nom_paciente ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_cuidador);
mysqli_stmt_execute($stmt);
$resultado  = mysqli_stmt_get_result($stmt);
$pacientes  = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

function calcularEdad($fecha_nacimiento)
{
    try {
        $nacimiento = new DateTime($fecha_nacimiento);
        $hoy        = new DateTime();
        $diff       = $nacimiento->diff($hoy);
        return $diff->y;
    } catch (Exception $e) {
        return null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>GeriaSmart</title>

    <style>
        /* Fondo fijo y layout reutilizado de otras páginas de cliente */
        .fixed-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgb(158, 234, 193) 0%, rgb(42, 191, 255) 100%);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            z-index: -1000;
        }

        .centrado {
            min-height: 100vh;
            position: relative;
            background: transparent;
            display: flex;
            justify-content: center;
            justify-items: center;
            align-items: center;
            padding: 40px 0;
            margin: 50px 0 0 0;
        }

        .centrado-flex {
            display: flex;
            flex-direction: column;
            align-items: center;
            align-content: center;
            justify-content: center;
            justify-items: center;
            width: 90%;
            min-height: 100%;
            padding: 10px 0;
        }

        .centrado-text-col {
            z-index: 2;
            margin: 0 auto;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }

        .opinion {
            align-items: flex-start;
            min-width: 80%;
            background-color: #ffffff;
        }

        .tabla-pacientes {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .tabla-pacientes th {
            background: var(--green);
            color: #ffffff;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        .tabla-pacientes td {
            padding: 15px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }

        .tabla-pacientes tr:last-child td {
            border-bottom: none;
        }

        .tabla-pacientes tr:hover {
            background: #f9f9f9;
        }

        .paciente-nombre {
            font-weight: 600;
            color: #333333;
            margin-bottom: 4px;
        }

        .paciente-detalle {
            font-size: 13px;
            color: #777777;
            margin: 0;
        }

        .badge-rol {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background: #e8f9f1;
            color: #1f7a4d;
            margin-top: 6px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tabla-pacientes tr:hover .action-buttons {
            opacity: 1;
        }

        .btn-desvincular, .btn-editar, .btn-historial, .btn-eliminar {
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            font-size: 12px;
        }

        .btn-desvincular {
            background-color: #dc3545;
        }

        .btn-eliminar {
            background-color: #dc3545;
        }

        .btn-editar {
            background-color: #ffc107;
        }

        .btn-historial {
            background-color: #17a2b8;
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

    <section class="centrado">
        <div class="container centrado-flex">
            <div class="centrado-text-col">
                <h1 class="reveal">Pacientes</h1>
                <div class="d-flex gap-2 justify-content-center flex-wrap reveal" style="margin-top: 10px;">
                    <a href="registrar_paciente.php?id_cuidador=<?php echo $_SESSION['id_usr']; ?>" class="btn btn-primary">
                        Registrar paciente
                    </a>
                    <a href="vincular_paciente.php?id_cuidador=<?php echo $_SESSION['id_usr']; ?>" class="btn btn-primary">
                        Sincronizar paciente
                    </a>
                </div>
                <p class="reveal" style="margin-bottom: 10px;">
                    Consulta los pacientes asociados a tu cuenta de cuidador
                </p>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success alert-dismissible fade show reveal">
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show reveal">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="reveal p-4">
                <?php if (count($pacientes) === 0): ?>
                    <h2 class="reveal">Aún no tienes pacientes vinculados</h2>
                    <p class="reveal" style="margin-top: 10px;">
                        Cuando vincules pacientes a tu cuenta de cuidador, aparecerán listados aquí.
                    </p>
                <?php else: ?>
                    <table class="tabla-pacientes reveal">
                        <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Datos clínicos</th>
                            <th>Contacto y vinculación</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pacientes as $paciente): ?>
                            <tr>
                                <td>
                                    <div class="paciente-nombre">
                                        <?= htmlspecialchars($paciente['nom_paciente']) ?>
                                    </div>
                                    <?php
                                    $edad = calcularEdad($paciente['fecha_nacimiento']);
                                    ?>
                                    <p class="paciente-detalle">
                                        <?php if ($edad !== null): ?>
                                            Edad: <?= intval($edad) ?> años
                                        <?php else: ?>
                                            Edad no disponible
                                        <?php endif; ?>
                                    </p>
                                    <span class="badge-rol">
                                        <?= $paciente['genero'] === 'femenino' ? 'Femenino' : 'Masculino' ?>
                                    </span>
                                </td>
                                <td>
                                    <p class="paciente-detalle">
                                        Peso: <?= htmlspecialchars($paciente['peso']) ?> kg
                                    </p>
                                    <p class="paciente-detalle">
                                        Estatura: <?= htmlspecialchars($paciente['estatura']) ?> cm
                                    </p>
                                    <p class="paciente-detalle">
                                        Padecimientos:
                                        <?php if (!empty($paciente['padecimientos'])): ?>
                                            <?= htmlspecialchars($paciente['padecimientos']) ?>
                                        <?php else: ?>
                                            Sin información registrada
                                        <?php endif; ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="paciente-detalle">
                                        Correo:
                                        <?php if (!empty($paciente['mail'])): ?>
                                            <?= htmlspecialchars($paciente['mail']) ?>
                                        <?php else: ?>
                                            No disponible
                                        <?php endif; ?>
                                    </p>
                                    <p class="paciente-detalle">
                                        Vinculado desde:
                                        <?= date('d/m/Y H:i', strtotime($paciente['fecha_vinculacion'])) ?>
                                    </p>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="#" class="btn-desvincular btn-desvincular-action" data-id="<?= $paciente['id_paciente'] ?>">Desvincular</a>
                                        <a href="#" class="btn-eliminar btn-eliminar-action" data-id="<?= $paciente['id_paciente'] ?>">Eliminar</a>
                                        <a href="editar_paciente.php?id_paciente=<?= $paciente['id_paciente'] ?>" class="btn-editar">Editar</a>
                                        <a href="historial.php?id_paciente=<?= $paciente['id_paciente'] ?>" class="btn-historial">Historial</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer>
    </footer>

    <div class="modal fade" id="desvincularModal" tabindex="-1" aria-labelledby="desvincularModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="desvincularModalLabel">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas desvincular a este paciente?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-cancel" id="confirmarDesvinculacionBtn">Confirmar desvinculación</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="eliminarPacienteModal" tabindex="-1" aria-labelledby="eliminarPacienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarPacienteModalLabel">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar a este paciente? Esta acción también lo desvinculará de tu cuenta y es irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-cancel" id="confirmarEliminarPacienteBtn">Confirmar eliminación</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/visible.js"></script>
    <script src="../assets/js/desvincular-paciente.js?v=2"></script>
    <script>
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-action');
        const modalEliminarElement = document.getElementById('eliminarPacienteModal');
        const confirmarEliminarBtn = document.getElementById('confirmarEliminarPacienteBtn');
        let idPacienteEliminar = null;

        if (modalEliminarElement && confirmarEliminarBtn) {
            const modalEliminar = new bootstrap.Modal(modalEliminarElement);

            botonesEliminar.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    idPacienteEliminar = this.getAttribute('data-id');
                    modalEliminar.show();
                });
            });

            confirmarEliminarBtn.addEventListener('click', function () {
                if (!idPacienteEliminar) {
                    return;
                }
                window.location.href = `eliminar_paciente.php?id_paciente=${idPacienteEliminar}&id_cuidador=<?php echo $id_cuidador; ?>`;
            });
        }
    </script>
</body>
</html>
