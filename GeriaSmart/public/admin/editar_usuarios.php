<?php
session_start();

if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 1) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

if(isset($_GET['id_usr'])) {
    echo "<script>
            alert('ID de usuario no válido.');
            window.location.href = 'usuarios.php';
          </script>";
    exit;
} else {
    $id_usr = isset($_GET['idusr']) ? intval($_GET['idusr']) : 0;
    require_once '../../config/db.php';

    $sql  = "SELECT nom_usr, mail, pass, tel_usr, tip_usu FROM usuario WHERE id_usr = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_usr);
    mysqli_stmt_execute($stmt);

    $resultado   = mysqli_stmt_get_result($stmt);
    $numero_rows = mysqli_num_rows($resultado);

    if (!$resultado || $numero_rows === 0) {
        echo "<script>
                alert('No existe un usuario con ese ID.');
                window.location.href = 'usuarios.php';
              </script>";
        exit;
    }

    //Aquí se asocian los datos en vez de usar un arreglo asociativo
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
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
<div class="main-content d-flex">

    <div id="admin-main" class="p-4">
        <h1 style="text-align: center; margin-bottom: 30px;">Editar usuario</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form id="usuariosForm" class="needs-validation border rounded p-3 bg-white" novalidate action="../../lib/gestor_usuarios.php" method="post">

            <input type="hidden" name="id_usr" value="<?= $id_usr ?>">
            <input type="hidden" name="tip_usu" value="<?= $usuario['tip_usu'] ?>">
            <input type="hidden" name="current_pass" value="<?= htmlspecialchars($usuario['pass']) ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre de usuario</label>
                    <input type="text"
                           id="nom_usr"
                           name="nom_usr"
                           class="form-control"
                           placeholder="Ej. Jonh"
                           minlength="2"
                           maxlength="100"
                           value="<?= htmlspecialchars($usuario['nom_usr']) ?>"
                           required>
                    <div class="valid-feedback">Nombre válido.</div>
                    <div class="invalid-feedback">Nombre no válido, usa 2-100 caracteres.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Correo</label>
                    <input type="email"
                           name="mail"
                           id="mail"
                           class="form-control"
                           placeholder="Ej. correo@ejemplo.com"
                           value="<?= htmlspecialchars($usuario['mail']) ?>"
                           required>
                    <div class="valid-feedback">Correo válido.</div>
                    <div class="invalid-feedback">Correo no válido.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password"
                           class="form-control"
                           id="pass"
                           name="pass"
                           placeholder="asd123!"
                           minlength="5"
                           maxlength="20"
                           value="<?= htmlspecialchars($usuario['pass']) ?>"
                           required>
                    <div class="valid-feedback">Contraseña válido.</div>
                    <div class="invalid-feedback">Contraseña no válida, usa 5-20 caracteres.</div>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password"
                           class="form-control"
                           id="confirmpassword"
                           name="confirmpassword"
                           placeholder="Confirma la contraseña"
                           minlength="5"
                           maxlength="20"
                           >
                    <div class="valid-feedback">Confirmación válida.</div>
                    <div class="invalid-feedback">Confirma la contraseña si la modificas.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input id="tel_usr"
                           name="tel_usr"
                           type="tel"
                           class="form-control"
                           placeholder="Ej. 4421234567"
                           value="<?= htmlspecialchars($usuario['tel_usr']) ?>"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           required
                    >
                    <div class="valid-feedback">Teléfono válido.</div>
                    <div class="invalid-feedback">Proporciona un teléfono de 10 dígitos.</div>
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary" type="submit" name="accion" value="editar">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script>
    var form = document.getElementById('usuariosForm');
    var controls = form.querySelectorAll('.form-control, .form-select');

    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            form.action = '../../lib/gestor_usuarios.php';
            form.method = 'post';
        }
        form.classList.add('was-validated');
    });
</script>
</body>
</html>