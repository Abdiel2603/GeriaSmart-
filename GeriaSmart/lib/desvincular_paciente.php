<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido.'];

if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    $response['message'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = isset($_POST['id_paciente']) ? intval($_POST['id_paciente']) : 0;
    $id_cuidador = isset($_POST['id_cuidador']) ? intval($_POST['id_cuidador']) : 0;

    if ($id_paciente > 0 && $id_cuidador > 0) {
        // Verificar que el cuidador que hace la petición es el que está logueado
        if (!isset($_SESSION['id_usr']) || $id_cuidador !== intval($_SESSION['id_usr'])) {
            $response['message'] = 'No tienes permiso para realizar esta acción.';
            echo json_encode($response);
            exit;
        }

        $sql = "DELETE FROM cuidador_paciente WHERE id_paciente = ? AND id_cuidador = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $id_paciente, $id_cuidador);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $response['success'] = true;
                $response['message'] = 'Paciente desvinculado exitosamente.';
            } else {
                $response['message'] = 'No se encontró la vinculación para eliminar.';
            }
        } else {
            $response['message'] = 'Error al intentar desvincular al paciente.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = 'Datos inválidos.';
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
