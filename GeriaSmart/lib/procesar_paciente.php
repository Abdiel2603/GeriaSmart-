<?php
@session_start();

// Este archivo se encarga exclusivamente de procesar los formularios
// de registro y edición de pacientes.

require_once __DIR__ . '/gestor_pacientes.php'; // Para usar las funciones de agregar/editar

// --- PROCESAMIENTO DEL FORMULARIO ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['accion'])) {
    // Redirigir si no es un POST o no hay acción.
    // Apuntamos a una página segura por defecto.
    header('Location: ../public/cliente/index.php');
    exit;
}

// Comprobación de seguridad de la sesión del cuidador
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 3 || !isset($_SESSION['id_usr'])) {
    header('Location: ../public/login.php?error=Acceso no autorizado');
    exit;
}

$id_cuidador_sesion = intval($_SESSION['id_usr']);
$accion = $_POST['accion'];

switch ($accion) {
    case 'registrar-paciente':
        $id_cuidador_form = isset($_POST['id_cuidador']) ? intval($_POST['id_cuidador']) : 0;
        // La redirección ahora es relativa a este archivo en /lib
        $redirect_url_error = "../public/cliente/registrar_paciente.php?id_cuidador={$id_cuidador_sesion}";

        if ($id_cuidador_form !== $id_cuidador_sesion) {
            header("Location: ../public/cliente/index.php?error=Operación no permitida.");
            exit;
        }

        // Recolección y validación de datos
        $nom_paciente = trim($_POST['nom_paciente'] ?? '');
        $mail = trim($_POST['mail'] ?? '');
        $pass = trim($_POST['pass'] ?? '');
        $confirm_pass = trim($_POST['confirm_pass'] ?? '');
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $peso = trim($_POST['peso'] ?? '');
        $estatura = trim($_POST['estatura'] ?? '');
        $padecimientos = trim($_POST['padecimientos'] ?? '');

        if (empty($nom_paciente) || empty($mail) || empty($pass) || empty($fecha_nacimiento) || empty($genero) || $peso === '' || $estatura === '') {
            header("Location: {$redirect_url_error}&error=" . urlencode('Todos los campos obligatorios deben estar llenos.'));
            exit;
        }
        if ($pass !== $confirm_pass) {
            header("Location: {$redirect_url_error}&error=" . urlencode('Las contraseñas no coinciden.'));
            exit;
        }

        $resultado = agregar_paciente($nom_paciente, $mail, $pass, $fecha_nacimiento, $genero, $peso, $estatura, $padecimientos);

        if ($resultado['estatus'] === 'exitoso') {
            $id_paciente = $resultado['id_paciente'];
            // La redirección ahora es relativa a este archivo en /lib
            header("Location: procesar_vinculacion.php?id_cuidador={$id_cuidador_sesion}&id_paciente={$id_paciente}");
        } else {
            header("Location: {$redirect_url_error}&error=" . urlencode($resultado['mensaje']));
        }
        exit;

    case 'editar-paciente':
        $id_paciente = isset($_POST['id_paciente']) ? intval($_POST['id_paciente']) : 0;
        $id_cuidador_form = isset($_POST['id_cuidador']) ? intval($_POST['id_cuidador']) : 0;
        $redirect_url_error = "../public/cliente/editar_paciente.php?id_paciente={$id_paciente}";

        if ($id_cuidador_form !== $id_cuidador_sesion) {
            header("Location: ../public/cliente/index.php?error=Operación no permitida.");
            exit;
        }

        // Recolección y validación de datos
        $nom_paciente = trim($_POST['nom_paciente'] ?? '');
        $mail = trim($_POST['mail'] ?? '');
        $pass = trim($_POST['pass'] ?? '');
        $confirm_pass = trim($_POST['confirm_pass'] ?? '');
        $current_pass = trim($_POST['current_pass'] ?? '');
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $peso = trim($_POST['peso'] ?? '');
        $estatura = trim($_POST['estatura'] ?? '');
        $padecimientos = trim($_POST['padecimientos'] ?? '');

        // Validaciones del lado del servidor
        if (empty($nom_paciente) || empty($mail) || empty($fecha_nacimiento) || empty($genero) || $peso === '' || $estatura === '') {
            header("Location: {$redirect_url_error}&error=" . urlencode('Todos los campos obligatorios deben estar llenos.'));
            exit;
        }
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
             header("Location: {$redirect_url_error}&error=" . urlencode('El correo electrónico no es válido.'));
            exit;
        }
        if ($pass !== $current_pass && $pass !== $confirm_pass) {
            header("Location: {$redirect_url_error}&error=" . urlencode('Las contraseñas no coinciden'));
            exit;
        }
        
        $resultado = editar_paciente($id_paciente, $nom_paciente, $mail, $pass, $fecha_nacimiento, $genero, $peso, $estatura, $padecimientos);

        if ($resultado['estatus'] === 'exitoso') {
            header("Location: ../public/cliente/pacientes_vinculados.php?id_usr={$id_cuidador_sesion}&msg=" . urlencode('Datos de paciente actualizados exitosamente'));
        } else {
            header("Location: {$redirect_url_error}&error=" . urlencode($resultado['mensaje']));
        }
        exit;

    default:
        header('Location: ../public/cliente/index.php?error=Acción no reconocida');
        exit;
}
