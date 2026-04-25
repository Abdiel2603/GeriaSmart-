<?php
@session_start();

require_once __DIR__ . '/../config/db.php';

// Validar sesión de cuidador
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 3 || !isset($_SESSION['id_usr'])) {
    header('Location: ../public/login.php?error=Acceso no autorizado');
    exit;
}

if (!isset($_GET['id_cuidador'], $_GET['id_paciente'])) {
    header('Location: ../public/cliente/index.php?error=' . urlencode('Datos de vinculación incompletos.'));
    exit;
}

$id_cuidador = intval($_GET['id_cuidador']);
$id_paciente = intval($_GET['id_paciente']);

// El cuidador del parámetro debe coincidir con la sesión
if ($id_cuidador !== intval($_SESSION['id_usr'])) {
    header('Location: ../public/cliente/index.php?error=' . urlencode('Solo puedes vincular pacientes a tu propia cuenta.'));
    exit;
}

// Verificar que el paciente exista
$sql_check_paciente = "SELECT id_paciente FROM paciente WHERE id_paciente = ?";
$stmt_check = mysqli_prepare($conn, $sql_check_paciente);
if (!$stmt_check) {
    header('Location: ../public/cliente/index.php?error=' . urlencode('Error al validar el paciente.'));
    exit;
}

mysqli_stmt_bind_param($stmt_check, 'i', $id_paciente);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if (mysqli_stmt_num_rows($stmt_check) === 0) {
    mysqli_stmt_close($stmt_check);
    header('Location: ../public/cliente/index.php?error=' . urlencode('El paciente especificado no existe.'));
    exit;
}

mysqli_stmt_close($stmt_check);

// Intentar vincular cuidador - paciente
$sql_vincular = "INSERT INTO cuidador_paciente (id_cuidador, id_paciente) VALUES (?, ?)";
$stmt_vin = mysqli_prepare($conn, $sql_vincular);

if (!$stmt_vin) {
    header('Location: ../public/cliente/index.php?error=' . urlencode('Error al preparar la vinculación.'));
    exit;
}

mysqli_stmt_bind_param($stmt_vin, 'ii', $id_cuidador, $id_paciente);
$ok = mysqli_stmt_execute($stmt_vin);

if (!$ok) {
    $codigo_error = mysqli_errno($conn);
    mysqli_stmt_close($stmt_vin);

    // 1062: registro duplicado (ya estaba vinculado)
    if ($codigo_error == 1062) {
        header('Location: ../public/cliente/pacientes_vinculados.php?id_usr=' . $id_cuidador . '&msg=' . urlencode('El paciente ya estaba vinculado a tu cuenta, se mantuvo la relación existente.'));
        exit;
    }

    header('Location: ../public/cliente/index.php?error=' . urlencode('No se pudo vincular el paciente con el cuidador.'));
    exit;
}

mysqli_stmt_close($stmt_vin);

header('Location: ../public/cliente/pacientes_vinculados.php?id_usr=' . $id_cuidador . '&msg=' . urlencode('Paciente registrado exitosamente'));
exit;

?>
