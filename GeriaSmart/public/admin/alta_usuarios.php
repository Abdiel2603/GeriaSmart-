<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 1) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$mensaje = "";
$error = "";
$alert_tipo = '';
$alert_texto = '';

if (isset($_GET['msg'])) {
    $alert_tipo  = 'success';
    $alert_texto = $_GET['msg'];
} elseif (isset($_GET['error'])) {
    $alert_tipo  = 'danger';
    $alert_texto = $_GET['error'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_usr = trim($_POST['nom_usr']);
    $mail = trim($_POST['mail']);
    $pass = trim($_POST['pass']);
    $dir_usr = trim($_POST['dir_usr']);
    $tip_usu = intval($_POST['tip_usu']);

    if (empty($nom_usr) || empty($mail) || empty($pass)) {
        $mensaje = "Por favor, complete todos los campos obligatorios.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electrónico no es válido.";
    } elseif (strlen($pass) < 5 || strlen($pass) > 20) {
        $mensaje = "La contraseña debe tener entre 5 y 20 caracteres.";
    } else {

        /* ==========================
           NUEVO: verificar correo repetido
           ========================== */
        $sqlCheck = "SELECT id_usr FROM usuario WHERE mail = ?";
        $stmtCheck = $conn->prepare($sqlCheck);

        if ($stmtCheck) {
            $stmtCheck->bind_param("s", $mail);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows > 0) {
                // Ya existe un usuario con ese correo
                $mensaje = "El correo electrónico ya está registrado.";
                $stmtCheck->close();
            } else {
                $stmtCheck->close();

                // ==========================
                // A partir de aquí va tu código ORIGINAL de INSERT
                // ==========================
                $sql = "INSERT INTO usuario (nom_usr, mail, pass, dir_usr, tip_usu) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("ssssi", $nom_usr, $mail, $pass, $dir_usr, $tip_usu);

                    if ($stmt->execute()) {
                        $mensaje = "Usuario registrado correctamente.";
                    } else {
                        $mensaje = "Error al registrar usuario.";
                    }

                    $stmt->close();
                } else {
                    $mensaje = "Error al preparar la consulta.";
                }
            }
        } else {
            $mensaje = "Error al verificar el correo electrónico.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar usuario</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        .form-label {
            color: #1a1a1a !important;
        }

        .productos {
            width: 90%;
        }

        .registrar-usuario{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
    </style>
</head>

<body>

<input type="checkbox" id="sidebar-toggle" class="sidebar-checkbox">

<label for="sidebar-toggle" class="sidebar-toggle">
    <i class="bi bi-list"></i>
</label>

<label for="sidebar-toggle" class="sidebar-overlay"></label>

<div class="sidebar">
    <div class="sidebar-header">
        <label for="sidebar-toggle" class="close-sidebar">
            <i class="bi bi-x-lg"></i>
        </label>
        <a href="../index.php">
            <img src="../assets/img/logo.png" alt="Logo">
        </a>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php">
            <i class="bi bi-house-door-fill"></i>
            <span>Inicio</span>
        </a>
        <a href="catalogo.php">
            <i class="bi bi-box-seam-fill"></i>
            <span>Productos</span>
        </a>
        <a href="usuarios.php" class="active">
            <i class="bi bi-people-fill"></i>
            <span>Gestión de usuarios</span>
        </a>
        <a href="../logout.php" style="color: #f61919">
            <i class="bi bi-box-arrow-right"></i>
            <span>Cerrar sesión</span>
        </a>
    </nav>
</div>
<!-- CONTENIDO -->
<div class="main-content d-flex">
    <div id="admin-main" class="productos p-4">
        <h1 style="text-align: center; margin-bottom: 30px">Crear nuevo usuario</h1>
        <?php if (!empty($alert_texto)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($alert_tipo); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($alert_texto); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>


        <form id="usuariosForm" class="needs-validation border rounded p-3 bg-white" novalidate>
            <div class="registrar-usuario row g-3">
                <div class="col-md-12">
                    <label class="form-label">Nombre completo</label>
                    <!-- Permitimos letras, números, espacios y algunos signos comunes; longitud 2-255 -->
                    <input type="text" id="nom_usr" name="nom_usr" class="form-control" placeholder="Ej. Jonh" minlength="2" maxlength="100" required>
                    <div class="valid-feedback">Nombre válido.</div>
                    <div class="invalid-feedback">Nombre no válido usa 2-100 caracteres.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Correo electrónico</label>
                    <!-- Permitimos letras, números, espacios y algunos signos comunes; longitud 2-255 -->
                    <input type="email" id="mail" name="mail" class="form-control" placeholder="correo@ejemplo.com" required>
                    <div class="valid-feedback">Correo válido.</div>
                    <div class="invalid-feedback">Correo no válido.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="pass" name="pass" class="form-control" placeholder="Una contraseña válida" minlength="5" maxlength="20" required>
                    <div class="valid-feedback">Contraseña válida.</div>
                    <div class="invalid-feedback">Contraseña no válida usa 5-20 caracteres.</div>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" placeholder="Confirma tu contraseña" minlength="5" maxlength="20" required>
                    <div class="valid-feedback">Confirmación válida.</div>
                    <div class="invalid-feedback">Debes confirmar la contraseña.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="tel_usr" id="tel_usr" class="form-control" placeholder="Ej. 4421234567" pattern="[0-9]{10}" maxlength="10" required>
                    <div class="valid-feedback">Teléfono válido.</div>
                    <div class="invalid-feedback">Proporciona un teléfono de 10 dígitos.</div>
                </div>

                <input type="hidden" name="tip_usu" value="3">
            </div>
            <div class="col-12 mt-3">
                <button class="btn btn-primary" type="submit" name="accion" value="agregar">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script>
    // Referencias
    var form = document.getElementById('usuariosForm');
    var controls = form.querySelectorAll('.form-control, .form-select');

    // Envío (solo demo: no navega; muestra mensaje si todo es válido)
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            // NO hagas preventDefault aquí
            form.action = '../../lib/gestor_usuarios.php';
            form.method = 'post';
            // deja que el submit nativo ocurra (así viaja name="accion")
        }
        form.classList.add('was-validated');
    });
</script>


</body>

</html>