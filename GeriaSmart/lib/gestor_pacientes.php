<?php
@session_start();

require_once __DIR__ . '/../config/db.php';

// --- FUNCIONES ---

function agregar_paciente($nom_paciente, $mail, $pass, $fecha_nacimiento, $genero, $peso, $estatura, $padecimientos = '')
{
    global $conn;

    $sql_check = "SELECT id_paciente FROM paciente WHERE mail = ?";
    $check_preparado = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($check_preparado, 's', $mail);
    mysqli_stmt_execute($check_preparado);
    mysqli_stmt_store_result($check_preparado);

    if (mysqli_stmt_num_rows($check_preparado) > 0) {
        mysqli_stmt_close($check_preparado);
        return ['estatus' => 'error', 'mensaje' => 'El correo electrónico ya está registrado.'];
    }
    mysqli_stmt_close($check_preparado);

    $sql = "INSERT INTO paciente (nom_paciente, mail, pass, fecha_nacimiento, genero, peso, estatura, padecimientos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    $padecimientos = $padecimientos ?? '';
    mysqli_stmt_bind_param($stmt, 'sssssiis', $nom_paciente, $mail, $pass, $fecha_nacimiento, $genero, $peso, $estatura, $padecimientos);

    if (mysqli_stmt_execute($stmt)) {
        $nuevo_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return ['estatus' => 'exitoso', 'id_paciente' => $nuevo_id];
    } else {
        mysqli_stmt_close($stmt);
        return ['estatus' => 'error', 'mensaje' => 'No se pudo registrar el paciente.'];
    }
}

function editar_paciente($id_paciente, $nom_paciente, $mail, $pass, $fecha_nacimiento, $genero, $peso, $estatura, $padecimientos)
{
    global $conn;

    // Verificar que el nuevo email no exista en otro paciente
    $sql_check = "SELECT id_paciente FROM paciente WHERE mail = ? AND id_paciente != ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 'si', $mail, $id_paciente);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        mysqli_stmt_close($stmt_check);
        return ['estatus' => 'error', 'mensaje' => 'El correo electrónico ya está en uso por otro paciente.'];
    }
    mysqli_stmt_close($stmt_check);
    
    $sql = "UPDATE paciente SET 
                nom_paciente = ?, 
                mail = ?, 
                pass = ?, 
                fecha_nacimiento = ?, 
                genero = ?, 
                peso = ?, 
                estatura = ?, 
                padecimientos = ?
            WHERE id_paciente = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    
    mysqli_stmt_bind_param(
        $stmt, 
        'sssssdisi',
        $nom_paciente,
        $mail,
        $pass,
        $fecha_nacimiento,
        $genero,
        $peso,
        $estatura,
        $padecimientos,
        $id_paciente
    );

    if (mysqli_stmt_execute($stmt)) {
        $filas_afectadas = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        // Si se afectaron filas o no (ningún cambio), se considera éxito
        return ['estatus' => 'exitoso'];
    } else {
        mysqli_stmt_close($stmt);
        return ['estatus' => 'error', 'mensaje' => 'Error al actualizar la base de datos.'];
    }
}

function obtener_paciente_por_id($conn, $id_paciente) {
    $sql = "SELECT * FROM paciente WHERE id_paciente = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_paciente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $paciente = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    return $paciente;
}

function obtener_citas_por_paciente($conn, $id_paciente) {
    $sql = "SELECT * FROM cita WHERE id_paciente = ? ORDER BY fecha DESC, hora DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_paciente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $citas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $citas;
}


