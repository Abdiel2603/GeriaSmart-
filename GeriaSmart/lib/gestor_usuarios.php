<?php
@session_start();

require_once __DIR__ . '/../config/db.php';

function mostrar_usuarios($busqueda = '') {
    global $conn;
    $sql = "SELECT * FROM usuario";
    if (!empty($busqueda)) {
        $sql .= " WHERE nom_usr LIKE ? OR mail LIKE ? OR id_usr LIKE ?";
        $select_preparado = mysqli_prepare($conn, $sql);
        $param = "%" . $busqueda . "%";
        mysqli_stmt_bind_param($select_preparado, 'sss', $param, $param, $param);
    } else {
        $select_preparado = mysqli_prepare($conn, $sql);
    }
    mysqli_stmt_execute($select_preparado);

    $resultado = mysqli_stmt_get_result($select_preparado);

    mysqli_stmt_close($select_preparado);
    $usuarios = array();
    while($fila_bd = mysqli_fetch_assoc($resultado)) {
        $usuarios[] = $fila_bd;
    }

    return $usuarios;

}

function agregar_usuario($nom_usr, $mail, $pass, $tel_usr, $tip_usu) {
    global $conn;

    // 1) Verificar si el correo ya existe
    $sql_check = "SELECT id_usr FROM usuario WHERE mail = ?";
    $check_preparado = mysqli_prepare($conn, $sql_check);

    if (!$check_preparado) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }

    mysqli_stmt_bind_param($check_preparado, 's', $mail);
    mysqli_stmt_execute($check_preparado);
    mysqli_stmt_store_result($check_preparado);

    if (mysqli_stmt_num_rows($check_preparado) > 0) {
        // Ya hay un usuario con ese correo
        mysqli_stmt_close($check_preparado);
        return [
            'estatus' => 'error',
            'mensaje' => 'El correo electrónico ya está registrado'
        ];
    }

    mysqli_stmt_close($check_preparado);
    $sql = "INSERT INTO usuario (nom_usr, mail, pass, tel_usr, tip_usu) VALUES (?, ?, ?, ?, ?)";
    $insertar_preparado = mysqli_prepare($conn, $sql);
    if(!$insertar_preparado) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }

    mysqli_stmt_bind_param($insertar_preparado, 'ssssi', $nom_usr, $mail, $pass, $tel_usr, $tip_usu);

    $query_ok = mysqli_stmt_execute($insertar_preparado);
    $rows_ok = mysqli_affected_rows($conn); // 0 >

    mysqli_stmt_close($insertar_preparado);

    if($query_ok && $rows_ok) {
        return [
            'estatus' => 'msg',
            'mensaje' => 'Usuario registrado correctamente'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error registrar el usuario'
        ];
    }
}

function editar_usuario($id_usr, $nom_usr, $mail, $pass, $tel_usr, $tip_usu) {
    global $conn;

    // 1) Verificar si el correo ya está usado por OTRO usuario
    $sql_check = "SELECT id_usr FROM usuario WHERE mail = ? AND id_usr <> ?";
    $check_preparado = mysqli_prepare($conn, $sql_check);

    if (!$check_preparado) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }

    mysqli_stmt_bind_param($check_preparado, 'si', $mail, $id_usr);
    mysqli_stmt_execute($check_preparado);
    mysqli_stmt_store_result($check_preparado);

    if (mysqli_stmt_num_rows($check_preparado) > 0) {
        // Otro usuario ya tiene ese correo
        mysqli_stmt_close($check_preparado);
        return [
            'estatus' => 'error',
            'mensaje' => 'El correo electrónico ya está registrado'
        ];
    }

    mysqli_stmt_close($check_preparado);
    $sql = "UPDATE usuario SET nom_usr = ?, mail = ?, pass = ?, tel_usr = ?, tip_usu = ? WHERE id_usr = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }

    mysqli_stmt_bind_param($stmt, 'ssssii', $nom_usr, $mail, $pass, $tel_usr, $tip_usu, $id_usr);
    $query_ok = mysqli_stmt_execute($stmt);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($query_ok && $rows_ok > 0) {
        // Actualizar datos de sesión si es el mismo usuario
        if(isset($_SESSION['id_usr']) && $_SESSION['id_usr'] == $id_usr) {
            $_SESSION['nom_usr'] = $nom_usr;
            $_SESSION['mail'] = $mail;
            $_SESSION['tel_usr'] = $tel_usr;
            $_SESSION['tipusu'] = $tip_usu;
        }
        
        return [
            'estatus' => 'exitoso',
            'mensaje' => 'Datos de cuenta actualizados correctamente ✔'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'No se pudo actualizar el usuario ❌'
        ];
    }
}


function eliminar_usuario($id_usr) {
    global $conn;
    $sql = "DELETE FROM usuario WHERE id_usr = ?";
    $delete_preparado = mysqli_prepare($conn, $sql); /* Valida que la query este bien y que ya se pueda ejecutar un execute bien */
    if(!$delete_preparado) {
        return [
            'estatus' => '',
            'mensaje' => 'Error en la ejecucion en la base de datos'
        ];
    }

    mysqli_stmt_bind_param($delete_preparado, 'i', $id_usr);
    $query_ok = mysqli_stmt_execute($delete_preparado);
    $rows_ok = mysqli_affected_rows($conn);

    if($query_ok && $rows_ok > 0) {
        return [
            'estatus' => 'exitoso',
            'mensaje' => 'Usuario eliminado exitosamente'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'Usuario no eliminado'
        ];
    }
}

//    $accion = "agregar";
//    $id_prod = 6;
//    $nom_usr = "Curso de Cobolt";
//    $desc = "Aprende a diseñar, programar y proteger sistemas bancarios con un lenguaje altamente robusto en cuanto a seguridad de información y acciones de este tipo.";
//    $prec = 1000;
//    $img = "https://www.esic.edu/sites/default/files/2024-02/lenguaje%20cobol.jpeg";
//    $estatus = 1;
//    $stock = 50;
//
//    switch($accion){
//        case 'agregar':
//            $resultado = agregar_producto($nom_usr, $desc, $prec, $img, $estatus, $stock);
//            print_r($resultado);
//            break;
//        case 'editar':
//            $resultado = editar_producto($id_prod, $nom_usr, $desc, $prec, $img, $estatus, $stock);
//            print_r($resultado);
//            break;
//        case 'eliminar':
//            $resultado = eliminar_producto($id_prod);
//            print_r($resultado);
//            break;
//        default:
//            echo 'Accion no encontrada, intente de nuevo';
//    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        switch ($accion) {
            case 'agregar':
                if (isset($_POST['nom_usr'], $_POST['mail'], $_POST['pass'], $_POST['confirmpassword'], $_POST['tel_usr'])) {
                    $nom_usr = trim($_POST['nom_usr']);
                    $mail = trim($_POST['mail']);
                    $pass = trim($_POST['pass']);
                    $confirmpassword = trim($_POST['confirmpassword']);
                    $tel_usr = trim($_POST['tel_usr']);
                    $tip_usu = (int)$_POST['tip_usu'];
                    
                    if ($pass !== $confirmpassword) {
                        header("Location: ../public/admin/alta_usuarios.php?error=" . urlencode('Las contraseñas no coinciden'));
                        exit;
                    }

                    $resultado = agregar_usuario($nom_usr, $mail, $pass, $tel_usr, $tip_usu);
                    if ($resultado['estatus'] === 'error') {
                        header("Location: ../public/admin/alta_usuarios.php?error=" . urlencode($resultado['mensaje']));
                    } else {
                        header("Location: ../public/admin/usuarios.php?msg=" . urlencode('Usuario creado exitosamente'));
                    }
                    exit;
                }
                break;

            case 'editar':
                if (isset($_POST['id_usr'], $_POST['nom_usr'], $_POST['mail'], $_POST['pass'], $_POST['confirmpassword'], $_POST['current_pass'], $_POST['tel_usr'], $_POST['tip_usu'])) {
                    $id_usr = intval($_POST['id_usr']);
                    $nom_usr = trim($_POST['nom_usr']);
                    $mail = trim($_POST['mail']);
                    $pass = trim($_POST['pass']);
                    $confirmpassword = trim($_POST['confirmpassword']);
                    $current_pass = trim($_POST['current_pass']);
                    $tel_usr = trim($_POST['tel_usr']);
                    $tip_usu = (int)$_POST['tip_usu'];
                    
                    if ($pass !== $current_pass && $pass !== $confirmpassword) {
                        header("Location: ../public/admin/editar_usuarios.php?idusr=" . $id_usr . "&error=" . urlencode('Las contraseñas no coinciden'));
                        exit;
                    }

                    $resultado = editar_usuario($id_usr, $nom_usr, $mail, $pass, $tel_usr, $tip_usu);
                    if ($resultado['estatus'] === 'error') {
                        header("Location: ../public/admin/editar_usuarios.php?idusr=" . $id_usr . "&error=" . urlencode($resultado['mensaje']));
                    } else {
                        header("Location: ../public/admin/usuarios.php?msg=" . urlencode('Datos de usuario actualizados exitosamente'));
                    }
                    exit;
                }
                break;

            case 'editar-usuario':
                if (isset($_POST['id_usr'], $_POST['nom_usr'], $_POST['mail'], $_POST['pass'], $_POST['confirmpassword'], $_POST['current_pass'], $_POST['tel_usr'])) {
                    $id_usr = intval($_POST['id_usr']);
                    $nom_usr = trim($_POST['nom_usr']);
                    $mail = trim($_POST['mail']);
                    $pass = trim($_POST['pass']);
                    $confirmpassword = trim($_POST['confirmpassword']);
                    $current_pass = trim($_POST['current_pass']);
                    $tel_usr = trim($_POST['tel_usr']);
                    $tip_usu = $_SESSION['tipusu']; //El tipo de usuario se mantiene igual, ya que no ocupamos modificarlo
                    
                    if ($pass !== $current_pass && $pass !== $confirmpassword) {
                        header("Location: ../public/cliente/editarinfo.php?id_usr=" . $id_usr . "&error=" . urlencode('Las contraseñas no coinciden'));
                        exit;
                    }

                    $resultado = editar_usuario($id_usr, $nom_usr, $mail, $pass, $tel_usr, $tip_usu);
                    header("Location: ../public/cliente/index.php?msg=" . urlencode($resultado['mensaje']));
                    exit;
                }
                break;

            case 'eliminar':
                $id_usr   = intval($_POST['id_usr']);
                $resultado = eliminar_usuario($id_usr);
                if ($resultado['estatus'] === 'exitoso') {
                    header("Location: ../public/admin/usuarios.php?msg=" . urlencode('Usuario eliminado exitosamente'));
                } else {
                    header("Location: ../public/admin/usuarios.php?error=" . urlencode($resultado['mensaje']));
                }
                break;
            default:
                echo 'Accion invalida, prueba otra distinta';

        }
    } else {
        echo "
            <script>
                alert('Accion no detectada, intente de nuevo');
                window.location.href = '../public/admin/usuarios.php';
            </script>
            ";
        exit;
    }
}

?>