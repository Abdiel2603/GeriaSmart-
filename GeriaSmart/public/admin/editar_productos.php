<?php
session_start();

if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 1) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

if(isset($_GET['id_prod'])) {
    echo "<script>
            alert('ID de producto no válido.');
            window.location.href = 'catalogo.php';
          </script>";
    exit;
} else {
    $id_prod = isset($_GET['idprod']) ? intval($_GET['idprod']) : 0;
    require_once '../../config/db.php';

    $sql  = "SELECT nom_prod, `desc`, modelo, prec, stock, img, estatus FROM catalogo WHERE id_prod = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_prod);
    mysqli_stmt_execute($stmt);

    $resultado   = mysqli_stmt_get_result($stmt);
    $numero_rows = mysqli_num_rows($resultado);

    if (!$resultado || $numero_rows === 0) {
        echo "<script>
                alert('No existe un producto con ese ID.');
                window.location.href = 'catalogo.php';
              </script>";
        exit;
    }

    //Aquí se asocian los datos en vez de usar un arreglo asociativo
    $producto = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
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
        <a href="catalogo.php" class="active">
            <i class="bi bi-box-seam-fill"></i>
            <span>Productos</span>
        </a>
        <a href="usuarios.php">
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
        <h1 style="text-align: center; margin-bottom: 30px;">Editar producto</h1>

        <?php if ($mensaje) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form id="productsForm" class="needs-validation border rounded p-3 bg-white" novalidate action="../../lib/gestor_productos.php" method="post">

            <input type="hidden" name="id_prod" value="<?= $id_prod ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre del producto</label>
                    <input type="text"
                           id="nom_prod"
                           name="nom_prod"
                           class="form-control"
                           placeholder="Ej. Curso de Front End"
                           minlength="2"
                           maxlength="255"
                           value="<?= htmlspecialchars($producto['nom_prod']) ?>"
                           required>
                    <div class="valid-feedback">Nombre válido.</div>
                    <div class="invalid-feedback">Nombre no válido, usa 2-255 caracteres.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Modelo</label>
                    <input type="text"
                           id="modelo"
                           name="modelo"
                           class="form-control"
                           placeholder="Ej. Basic"
                           minlength="3"
                           maxlength="20"
                           value="<?= htmlspecialchars($producto['modelo']) ?>"
                           required>
                    <div class="valid-feedback">Modelo válido.</div>
                    <div class="invalid-feedback">NModelo no válido, usa 3-20 caracteres.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Precio</label>
                    <input type="number"
                           name="prec"
                           id="prec"
                           class="form-control"
                           min="1"
                           step="0.01"
                           placeholder="Ej. 499.00"
                           value="<?= htmlspecialchars($producto['prec']) ?>"
                           required>
                    <div class="valid-feedback">Precio válido.</div>
                    <div class="invalid-feedback">Ingresa un precio válido (mayor a 0, con 2 decimales).</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Stock</label>
                    <input type="number"
                           class="form-control"
                           id="stock"
                           name="stock"
                           min="0"
                           step="1"
                           placeholder="Ej. 25"
                           value="<?= htmlspecialchars($producto['stock']) ?>"
                           required>
                    <div class="valid-feedback">Stock válido.</div>
                    <div class="invalid-feedback">Ingresa un número entero mayor o igual a 0.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Imagen (URL)</label>
                    <input id="img"
                           name="img"
                           type="url"
                           class="form-control"
                           placeholder="https://sitio/img/smb3.jpg"
                           minlength="1"
                           maxlength="500"
                           value="<?= htmlspecialchars($producto['img']) ?>"
                           required>
                    <div class="valid-feedback">Ok.</div>
                    <div class="invalid-feedback">Proporciona una ruta URL correcta de máximo 500 caracteres.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" id="estatus" class="form-select" required>
                        <option value="1" <?= $producto['estatus'] == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= $producto['estatus'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>


                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="desc"
                              id="desc"
                              rows="4"
                              class="form-control"
                              minlength="20"
                              maxlength="500"
                              placeholder="Describe el producto..."
                              required><?= htmlspecialchars($producto['desc']) ?></textarea>
                    <div class="valid-feedback">Ok.</div>
                    <div class="invalid-feedback">Escribe entre 20 y 500 caracteres.</div>
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary" type="submit" name="accion" value="editar">Actualizar producto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script>
    var form = document.getElementById('productsForm');
    var controls = form.querySelectorAll('.form-control, .form-select');

    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            form.action = '../../lib/gestor_productos.php';
            form.method = 'post';
        }
        form.classList.add('was-validated');
    });
</script>
</body>
</html>