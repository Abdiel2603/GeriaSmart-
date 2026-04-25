<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario esté logueado y sea un cuidador
if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

// Validar que los IDs necesarios estén presentes
if (!isset($_GET['id_paciente']) || !isset($_GET['id_cuidador'])) {
    header("Location: pacientes_vinculados.php?id_usr=" . $_SESSION['id_usr'] . "&error=Información insuficiente para procesar la solicitud.");
    exit;
}

$id_paciente = intval($_GET['id_paciente']);
$id_cuidador_get = intval($_GET['id_cuidador']);
$id_cuidador_sesion = intval($_SESSION['id_usr']);

// Verificar que el cuidador que solicita la eliminación es el mismo que está logueado
if ($id_cuidador_get !== $id_cuidador_sesion) {
    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador_sesion . "&error=No tienes permiso para realizar esta acción.");
    exit;
}

// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // 1. Desvincular al paciente del cuidador
    $sql_desvincular = "DELETE FROM cuidador_paciente WHERE id_paciente = ? AND id_cuidador = ?";
    $stmt_desvincular = mysqli_prepare($conn, $sql_desvincular);
    mysqli_stmt_bind_param($stmt_desvincular, 'ii', $id_paciente, $id_cuidador_sesion);
    
    if (!mysqli_stmt_execute($stmt_desvincular)) {
        throw new Exception("Error al desvincular al paciente.");
    }
    mysqli_stmt_close($stmt_desvincular);

    // 2. Eliminar al paciente del sistema
    $sql_eliminar = "DELETE FROM paciente WHERE id_paciente = ?";
    $stmt_eliminar = mysqli_prepare($conn, $sql_eliminar);
    mysqli_stmt_bind_param($stmt_eliminar, 'i', $id_paciente);

    if (!mysqli_stmt_execute($stmt_eliminar)) {
        throw new Exception("Error al eliminar al paciente del sistema.");
    }
    mysqli_stmt_close($stmt_eliminar);

    // Si todo fue bien, confirmar la transacción
    mysqli_commit($conn);

    $msg = "Paciente eliminado exitosamente";
    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador_sesion . "&msg=" . urlencode($msg));

} catch (Exception $e) {
    // Si algo falló, revertir la transacción
    mysqli_rollback($conn);
    $error = $e->getMessage();
    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador_sesion . "&error=" . urlencode($error));
} finally {
    mysqli_close($conn);
    exit;
}
?>
