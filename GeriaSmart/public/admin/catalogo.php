<?php
session_start();
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 1) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Admin</title>
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

    <!-- Contenido principal de la página -->
    <div class="main-content">
        <section class="users" id="usuarios">
            <div class="container">
                <h1>Gestionar productos</h1>
                <h2>Crea, modifica o elimina productos.</h2>

                <div class="crud-btns" style="margin-bottom: 60px;">
                    <a href="./alta_productos.php" class="btn">+ Añadir producto</a>
                </div>
                <h2>Productos disponibles</h2>
                <div class="users-list">
                    <table class="table table-stripped">
                        <thead>
                        <tr>
                            <th >ID</th>
                            <th>Nombre producto</th>
                            <th>Descripción</th>
                            <th>Modelo</th>
                            <th>Precio</th>
                            <th class="w-25">Imagen</th>
                            <th>Estatus</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        require_once __DIR__ . '/../../lib/gestor_productos.php';
                        $productos = mostrar_productos();
                        foreach($productos as $fila_tabla) {
                            ?>
                            <tr>
                                <td><?= $fila_tabla['id_prod'] ?></td>
                                <td><?= $fila_tabla['nom_prod'] ?></td>
                                <td><?= $fila_tabla['desc'] ?></td>
                                <td><?= $fila_tabla['modelo'] ?></td>
                                <td>$<?= $fila_tabla['prec'] ?> MXN</td>
                                <td>
                                    <img style="box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);" class="img-fluid" src="<?= $fila_tabla['img'] ?>" alt="<?= $fila_tabla['nom_prod'] ?>">
                                </td>
                                <td><?= $fila_tabla['estatus'] ? "Activo ✔" : "Inactivo ❌" ?></td>
                                <td><?= $fila_tabla['stock'] ?></td>
                                <td style="height: 100%; display: flex; flex-direction: column; gap: 30px; margin: 30% 0 10% 0;">
                                    <a style="box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);" href="editar_productos.php?idprod=<?= $fila_tabla['id_prod'] ?>" class="btn-edit btn-warning">Editar</a>
                                    <form action="catalogo.php" method="post" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                        <input type="hidden" name="id_prod" value="<?= $fila_tabla['id_prod'] ?>">
                                        <button style="box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);" type="submit" name="accion" value="eliminar" class="btn-delete btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

<footer>
</footer>
</body>
</html>
