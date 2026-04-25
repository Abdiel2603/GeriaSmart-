<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mail'], $_POST['pass'])) {

        require_once __DIR__ . '../../config/db.php';
        $mail = trim($_POST['mail']);
        $password = trim($_POST['pass']);

        $sql = "SELECT id_usr, nom_usr, mail, pass, tel_usr, tip_usu FROM usuario WHERE mail = ?";
        $query_preparada = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($query_preparada, "s", $mail);
        mysqli_stmt_execute($query_preparada);

        $resultado = mysqli_stmt_get_result($query_preparada);
        mysqli_stmt_close($query_preparada);

        if ($resultado && mysqli_num_rows($resultado) == 1) {
            $usuario = mysqli_fetch_assoc($resultado);

            // Si usas password_hash en BD, cambia esta línea por password_verify(...)
            if ($usuario['pass'] == $password) {

                // Autenticacion correcta
                $_SESSION['id_usr']  = $usuario['id_usr'];
                $_SESSION['nom_usr'] = $usuario['nom_usr'];
                $_SESSION['mail']    = $usuario['mail'];
                $_SESSION['tel_usr'] = $usuario['tel_usr'];
                $_SESSION['tipusu']  = $usuario['tip_usu'];

                mysqli_close($conn);

                /* ===== AQUI VIENE LO NUEVO IMPORTANTE =====
                   Si antes de iniciar sesión se quiso comprar un producto,
                   guardaste su id en $_SESSION['id_prod_comprar'] (por ejemplo
                   desde productodetalles.php cuando el usuario no ha iniciado sesión).
                   Después de loguearse, lo mandamos a formulario_tarjeta.php.
                */
                if (isset($_SESSION['id_prod_comprar'])) {
                    $idProd = (int) $_SESSION['id_prod_comprar'];
                    unset($_SESSION['id_prod_comprar']);
                    header('Location: ../public/formulario_tarjeta.php?id_prod=' . $idProd);
                    exit;
                }
                /* ===== FIN DEL BLOQUE NUEVO ===== */

                if($usuario['tip_usu'] == 1) {
                    header('Location: ../public/admin/index.php');
                    exit;
                } elseif($usuario['tip_usu'] == 2) {
                    header('Location: ../public/cliente/index.php');
                    exit;
                } elseif($usuario['tip_usu'] == 3) {
                    header('Location: ../public/cliente/index.php');
                    exit;
                } else {
                    header('Location: ../public/login.php?error=' . urlencode('Usuario no reconocido'));
                    exit;
                }
            } else {
                header('Location: ../public/login.php?error=' . urlencode('Credenciales incorrectas'));
                exit;
            }
        } else {
            header('Location: ../public/login.php?error=' . urlencode('Correo no encontrado'));
            exit;
        }

    } else {
        header('Location: ../public/login.php?error=' . urlencode('Completa el formulario'));
        exit;
    }
} else {
    header('Location: ../public/login.php?error=' . urlencode('Acceso no permitido'));
    exit;
}
?>
