<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_usr'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id_prod'])) {
    header("Location: productos.php");
    exit;
}

$id_prod = intval($_GET['id_prod']);

// Obtener datos del producto
$sql = "SELECT id_prod, modelo, nom_prod, prec FROM catalogo WHERE id_prod = ? AND estatus = 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_prod);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if (mysqli_num_rows($resultado) === 0) {
    header("Location: productos.php?error=Producto no encontrado");
    exit;
}

$producto = mysqli_fetch_assoc($resultado);
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/inicio.css">
    <link rel="stylesheet" href="./assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="./assets/css/animations.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Datos de Pago - GeriaSmart</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { min-height: 100vh; padding: 20px; }
        .container { max-width: 500px; margin: 50px auto; }
        .card-tarjeta {
            background: var(--glass-card-background);
            border-radius: 8px;
            padding: 30px;
            align-content: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
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
    <div class="card-tarjeta">
        <h1 class="reveal">Completa tu compra</h1>

        <?php if (!empty($error)): ?>
            <div class="error reveal" style="text-align: center; margin-bottom: 15px; background-color: rgba(255,0,0,0.32); border-radius: 5px">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="resumen reveal">
            <p><strong>Producto:</strong></p>
            <p><?= htmlspecialchars($producto['nom_prod']) ?> <?= htmlspecialchars($producto['modelo']) ?></p>
            <p style="margin-top: 15px;"><strong>Precio:</strong></p>
            <div class="total">$<?= number_format($producto['prec'], 2) ?> MXN</div>
        </div>

        <form class="form-fields" action="confirmar_compra.php" method="POST">
            <input type="hidden" name="id_prod" value="<?= $id_prod ?>">

            <div class="form-field reveal">
                <label class="form-label" for="nombre_titular">Titular de la tarjeta</label>
                <input type="text" id="nombre_titular" name="nombre_titular" placeholder="Juan Pérez" required>
            </div>

            <div class="form-field reveal">
                <label class="form-label" for="nombre_titular">Correo electrónico de la cuenta</label>
                <input type="email" id="correo_compra" name="correo_compra" placeholder="Tu correo electrónico  " required>
            </div>

            <div class="form-field reveal">
                <label class="form-label" for="tarjeta">Número de tarjeta</label>
                <input type="text" id="tarjeta" name="tarjeta" placeholder="1234 5678 9012 3456" maxlength="19"
                       pattern="[0-9 ]{16,19}" required>
            </div>

                <div class="form-field reveal">
                    <label class="form-label" for="vence">Vencimiento (MM/YY)</label>
                    <input type="text" id="vence" name="vence" placeholder="12/25" maxlength="5"
                           pattern="[0-9]{2}/[0-9]{2}" required>
                </div>
                <div class="form-field reveal">
                    <label class="form-label" for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" pattern="[0-9]{3,4}" required>
                </div>

            <div class="btns reveal">
                <a class="btn-secondary" href="productos.php">Cancelar</a>
                <button type="submit" class="btn-primary">Confirmar compra</button>
            </div>
        </form>
    </div>
</div>
<script src="./assets/js/visible.js"></script>
</body>
</html>
