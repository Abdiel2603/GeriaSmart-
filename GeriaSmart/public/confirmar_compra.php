<?php
session_start();

// 1. Verificar que venga por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: productos.php');
    exit;
}

// 2. Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usr'])) {
    // Si no hay sesión, regresar al login
    header('Location: login.php?error=' . urlencode('Inicia sesión para completar tu compra.'));
    exit;
}

// 3. Conexión a la BD
require_once __DIR__ . '/../config/db.php';

// 4. Recibir y limpiar datos del formulario
$id_usr   = (int) $_SESSION['id_usr'];
$id_prod  = isset($_POST['id_prod']) ? (int) $_POST['id_prod'] : 0;
$nombre_titular = trim($_POST['nombre_titular'] ?? '');
$correo_form = trim($_POST['correo_compra'] ?? '');
$tarjeta        = trim($_POST['tarjeta'] ?? '');
$vence          = trim($_POST['vence'] ?? '');
$cvv            = trim($_POST['cvv'] ?? '');

// Validación sencilla
if ($id_prod <= 0 || $nombre_titular === '' || $correo_form === '' || $tarjeta === '' || $vence === '' || $cvv === '') {
    header('Location: formulario_tarjeta.php?id_prod=' . $id_prod . '&error=' . urlencode('Completa todos los campos.'));
    exit;
}

$correoSesion = $_SESSION['mail'] ?? '';

if (strcasecmp($correo_form, $correoSesion) !== 0) {
    header('Location: formulario_tarjeta.php?id_prod=' . $id_prod . '&error=' . urlencode('El correo no coincide con el de tu cuenta.'));
    exit;
}


// 5. Obtener datos del producto (precio, stock, etc.)
$sqlProd = "SELECT prec, stock FROM catalogo WHERE id_prod = ? AND estatus = 1";
$stmtProd = mysqli_prepare($conn, $sqlProd);
mysqli_stmt_bind_param($stmtProd, 'i', $id_prod);
mysqli_stmt_execute($stmtProd);
$resProd = mysqli_stmt_get_result($stmtProd);
mysqli_stmt_close($stmtProd);

if (!$resProd || mysqli_num_rows($resProd) === 0) {
    header('Location: productos.php?error=' . urlencode('Producto no disponible.'));
    exit;
}

$prod = mysqli_fetch_assoc($resProd);

// Opcional: validar stock > 0
if ((int)$prod['stock'] <= 0) {
    header('Location: productodetalles.php?idprod=' . $id_prod . '&error=' . urlencode('Sin stock disponible.'));
    exit;
}

// 6. Registrar la compra en tabla compras (ajusta nombres de tabla/campos)
$sqlCompra = "INSERT INTO compras (id_usr, id_prod, total, fecha_compra) 
              VALUES (?, ?, ?, NOW())";
$stmtCompra = mysqli_prepare($conn, $sqlCompra);
$total = (float)$prod['prec'];
mysqli_stmt_bind_param($stmtCompra, 'iid', $id_usr, $id_prod, $total);
$okCompra = mysqli_stmt_execute($stmtCompra);
$id_compra = mysqli_insert_id($conn);
mysqli_stmt_close($stmtCompra);

// 7. Si la inserción fue correcta, actualizar stock
if ($okCompra) {
    $sqlStock = "UPDATE catalogo SET stock = stock - 1 WHERE id_prod = ? AND stock > 0";
    $stmtStock = mysqli_prepare($conn, $sqlStock);
    mysqli_stmt_bind_param($stmtStock, 'i', $id_prod);
    mysqli_stmt_execute($stmtStock);
    mysqli_stmt_close($stmtStock);

    mysqli_close($conn);
} else {
    mysqli_close($conn);
    header('Location: formulario_tarjeta.php?id_prod=' . $id_prod . '&error=' . urlencode('Error al registrar la compra.'));
    exit;
}

// 8. Mostrar pantalla de confirmación simple
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/inicio.css">
    <link rel="stylesheet" href="./assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="./assets/css/animations.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Compra confirmada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            min-height: 100vh; 
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .container { 
            max-width: 500px; 
            width: 100%;
            margin: 20px auto;
            text-align: center;
        }
        h1 { color: #333; margin-bottom: 20px; text-align: center; }
        .resumen {
            background: rgba(0, 0, 0, 0.15);
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid var(--green);
        }

        .fixed-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgb(158, 234, 193) 0%, rgb(42, 191, 255) 100%);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            z-index: -1000;
        }
    </style>
</head>
<body>
<div class="fixed-background"></div>
<div class="container">
    <h1>¡Gracias por tu compra!</h1>
    <p>Tu número de pedido es: <strong>#<?= htmlspecialchars($id_compra) ?></strong></p>
    <p>Monto pagado: <strong>$<?= number_format($total, 2) ?> MXN</strong></p>
    <a href="productos.php" class="btn btn-primary">Volver al catálogo</a>
</div>
</body>
</html>
