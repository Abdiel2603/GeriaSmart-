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

// --- Obtención de Datos ---
$paciente = obtener_paciente_por_id($conn, $id_paciente);
if (!$paciente) {
    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador . "&error=Paciente no encontrado");
    exit;
}

$citas = obtener_citas_por_paciente($conn, $id_paciente);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Citas - <?= htmlspecialchars($paciente['nom_paciente']) ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
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
        .tabla-citas {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-collapse: collapse;
        }
        .tabla-citas th, .tabla-citas td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .tabla-citas th {
            background-color: var(--green);
            color: white;
            font-weight: bold;
        }
        .tabla-citas tbody tr:last-child td {
            border-bottom: none;
        }
        .tabla-citas tbody tr:hover {
            background-color: #f8f9fa;
        }
        .titulo-cita {
            font-weight: bold;
            color: #333;
        }
        .fecha-hora {
            color: #555;
        }
        .descripcion-cita {
            color: #777;
            font-size: 0.9em;
        }
        .btn-volver {
            margin-top: 20px;
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link" href="mis_compras.php">Mis compras</a></li>
                    <li class="nav-item"><a class="nav-link" href="pacientes_vinculados.php?id_usr=<?= $_SESSION['id_usr'] ?>">Pacientes</a></li>
                    <li class="nav-item"><a class="nav-link" href="editarinfo.php?id_usr=<?= $_SESSION['id_usr'] ?>">Configuración</a></li>
                </ul>
                <div class="navbar-section navbar-section-login">
                    <a class="btn-void nav-login-btn" href="#"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['nom_usr']) ?></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="text-center mb-4">
            <h1>Historial de Citas</h1>
            <h2><?= htmlspecialchars($paciente['nom_paciente']) ?></h2>
            <a href="historial.php?id_paciente=<?= $id_paciente ?>" class="btn btn-light mt-2">
                <i class="bi bi-arrow-left"></i> Volver al Historial Principal
            </a>
        </div>

        <?php if (empty($citas)): ?>
            <div class="text-center alert alert-info" style="max-width: 800px; margin: 20px auto;">
                <p>No hay citas registradas para este paciente.</p>
            </div>
        <?php else: ?>
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Motivo de la Cita</th>
                        <th>Fecha y Hora</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td>
                                <div class="titulo-cita"><?= htmlspecialchars($cita['titulo']) ?></div>
                            </td>
                            <td>
                                <div class="fecha-hora"><?= date('d/m/Y', strtotime($cita['fecha'])) ?> a las <?= htmlspecialchars($cita['hora']) ?></div>
                            </td>
                            <td>
                                <div class="descripcion-cita"><?= htmlspecialchars($cita['descripcion']) ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
