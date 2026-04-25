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
    <title>Panel administrador</title>
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
            <a href="index.php" class="active">
                <i class="bi bi-house-door-fill"></i>
                <span>Inicio</span>
            </a>
            <a href="catalogo.php">
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

    <div class="main-content">
        <section class="users" id="admin-home">
            <div class="container">
                <h1>Panel de administrador</h1>
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <h2>
                    Bienvenido al panel de administrador <?= htmlspecialchars($_SESSION['nom_usr'] ?? 'Administrador') ?>.
                </h2>
            </div>
        </section>
    </div>

    <footer>
    </footer>
</body>
</html>
