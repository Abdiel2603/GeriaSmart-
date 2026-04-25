<?php
session_start();
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 1) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}


$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar producto</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        .form-label {
            color: #1a1a1a !important;
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
<!-- CONTENIDO -->
<div class="main-content d-flex">
    <div id="admin-main" class="p-4">
        <h1 style="text-align: center; margin-bottom: 30px">Agregar producto</h1>
        <?php
        if ($mensaje) {
            ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>
        <form id="productsForm" class="needs-validation border rounded p-3 bg-white" novalidate>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Nombre del producto</label>
                    <!-- Permitimos letras, números, espacios y algunos signos comunes; longitud 2-255 -->
                    <input type="text" id="nom_prod" name="nom_prod" class="form-control" placeholder="Ej. GeriaBand Watch" minlength="2" maxlength="255" required>
                    <div class="valid-feedback">Nombre válido.</div>
                    <div class="invalid-feedback">Nombre no válido usa 2-255 caracteres.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Modelo</label>
                    <!-- Permitimos letras, números, espacios y algunos signos comunes; longitud 2-255 -->
                    <input type="text" id="nom_prod" name="nom_prod" class="form-control" placeholder="Ej. Basic" minlength="3" maxlength="20" required>
                    <div class="valid-feedback">Modelo válido.</div>
                    <div class="invalid-feedback">Modelo no válido usa 3-20 caracteres.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Precio</label>
                    <input type="number" name="prec" id="prec" class="form-control" min="1" step="0.01" placeholder="Ej. 499.00" required>
                    <div class="valid-feedback">Precio válido.</div>
                    <div class="invalid-feedback">Ingresa un precio válido (mayor a 0, con 2 decimales).</div>
                </div>


                <div class="col-md-3">
                    <label class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" min="1" step="1" placeholder="Ej. 25" required>
                    <div class="valid-feedback">Stock válido.</div>
                    <div class="invalid-feedback">Ingresa un número entero mayor o igual a 1.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Imagen (URL)</label>
                    <!-- Cadena 1-500; si quieres, luego validamos extensión en PHP -->
                    <input id="img" name="img" type="url" class="form-control"
                           placeholder="https://sitio/img/smb3.jpg" minlength="1"
                           maxlength="500" pattern="^https?:/\S+\.(jpg|jpeg|png)$" required>
                    <!--
                      https?:// Permite http y https
                      \S+     Cualquier caracter y cadena sin espacios
                      \.      Punto para la extensión
                      (jpg|jpeg|png) Extenciones permitidad

                      (?=.*[A-Z])     -> al menos una mayúscula en cualquier posición.
                      (?=.*[a-z])     -> al menos una minúscula.
                      (?=.*\d)        -> al menos un dígito.
                      (?=.*[@#$%&..]) -> al menos un símbolo de ese set.

                      pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@#$%&..]).{8,}$"
                    -->
                    <div class="valid-feedback">Ok.</div>
                    <div class="invalid-feedback">Proporciona una ruta URL correcta de máximo 500 caracteres.</div>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="desc" id="desc" rows="4" class="form-control" minlength="20" maxlength="500" placeholder="Describe el producto..." required></textarea>
                    <div class="valid-feedback">Ok.</div>
                    <div class="invalid-feedback">Escribe entre 20 y 500 caracteres.</div>
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary" type="submit" name="accion" value="agregar">Agregar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script>
    // Referencias
    var form = document.getElementById('productsForm');
    var controls = form.querySelectorAll('.form-control, .form-select');

    // Envío (solo demo: no navega; muestra mensaje si todo es válido)
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            // NO hagas preventDefault aquí
            form.action = '../../lib/gestor_productos.php';
            form.method = 'post';
            // deja que el submit nativo ocurra (así viaja name="accion")
        }
        form.classList.add('was-validated');
    });
</script>


</body>

</html>