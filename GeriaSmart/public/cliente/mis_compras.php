<?php
session_start();
require_once '../../config/db.php';

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';


if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$id_usr = $_SESSION['id_usr'];

// JOIN: compras → catalogo para obtener datos del producto
$sql = "SELECT 
            c.id_compra,
            c.fecha_compra,
            p.id_prod,
            p.nom_prod,
            p.prec,
            p.img,
            p.modelo,
            c.num_tarjeta
        FROM compras c
        JOIN catalogo p ON c.id_prod = p.id_prod
        WHERE c.id_usr = ?
        ORDER BY c.fecha_compra DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_usr);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$compras = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/inicio_responsive.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>GeriaSmart</title>

    <style>
        /* ==========================================================================
      Todo lo de la pagina
      ========================================================================== */
        /* Fondo fijo */
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

        /* ==============================================================================
        centrado
        =============================================================================== */

        .centrado {
            min-height: 100vh;
            position: relative;
            background: transparent;
            display: flex;
            justify-content: center;
            justify-items: center;
            align-items: center; /* Centrado vertical */
            padding: 40px 0;
            margin: 50px 0 0 0;
        }

        .centrado-flex {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrado vertical */
            align-content: center;
            justify-content: center;
            justify-items: center; /* Centrado vertical */
            width: 90%;
            min-height: 100%; /* Ocupa toda la altura disponible */
            padding: 10px 0;
        }

        .centrado-text-col {
            z-index: 2;
            margin: 0 auto;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }

        .centrado-img {
            width: 250px;
            height: auto;
            opacity: 0;
            max-width: 600px;
            object-fit: contain;
            filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.3));
            animation: fadeUp .75s ease forwards;
        }

        .opinion {
            align-items: flex-start;
            min-width: 80%;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th {
            background: var(--green);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .producto-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .producto-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .producto-datos h4 {
            color: #333;
            margin-bottom: 5px;
        }
        .producto-datos p {
            color: #999;
            font-size: 12px;
        }
        .precio {
            color: #27ae60;
            font-weight: bold;
            font-size: 16px;
        }
        .fecha {
            color: #999;
            font-size: 14px;
        }
        .tarjeta {
            color: #666;
            font-size: 12px;
        }

    </style>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container navbar-container">
            <!-- Logo a la izquierda -->
            <div class="navbar-section navbar-section-logo">
                <a href="../index.php">
                    <img class="navbar-brand logo" src="../assets/img/logo.png" alt="Logo">
                </a>
            </div>

            <!-- Botón de menú para teléfonos -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menú de navegación centrado -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="./index.php">
                            <i class="bi bi-person"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="mis_compras.php">
                            <i class="bi bi-box-seam"></i>
                            <span>Mis compras</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>"">
                            <i class="bi bi-person-fill-add"></i>
                            <span>Pacientes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="editarinfo.php?id_usr=<?php echo $_SESSION['id_usr']; ?>"">
                            <i class="bi bi-gear"></i>
                            <span>Configuración</span>
                        </a>
                    </li>
                </ul>

                <!-- Botón de inicio de sesión a la derecha -->
                <div class="navbar-section navbar-section-login">
                    <a class="btn-void nav-login-btn navbar-icon-text" href="#">
                        <i class="bi bi-person-circle"></i>
                        <span><?php echo isset($_SESSION['nom_usr']) ? $_SESSION['nom_usr'] : 'Usuario'; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</head>
<body>
    <div class="fixed-background"></div>

<section class="centrado">
    <div class="container centrado-flex">
        <!-- Columna izquierda-->
        <div class="centrado-text-col">
            <h1 class="reveal">Mis compras</h1>
            <p class="reveal" style="margin-bottom: 10px;">
                Consulta el historial de los productos que has comprado
            </p>
            <?php if (count($compras) === 0): ?>
                <div class="sin-compras">
                    <h2 style="margin-top: 20px;" class="reveal">Aún no has realizado compras</h2>
                    <p style="margin-top: 10px;" class="reveal">
                        <a href="../productos.php" style="color: #2a774f; text-decoration: none;">Ver catálogo de productos →</a>
                    </p>
                </div>
            <?php else: ?>
                <table class="reveal">
                    <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Fecha de compra</th>
                        <th>Tarjeta</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td>
                                <div class="producto-info reveal">
                                    <img src="<?= htmlspecialchars($compra['img']) ?>" alt="" class="producto-img">
                                    <div class="producto-datos">
                                        <h4><?= htmlspecialchars($compra['nom_prod']) ?></h4>
                                        <p>Modelo: <?= htmlspecialchars($compra['modelo']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="precio">$<?= number_format($compra['prec'], 2) ?></td>
                            <td class="fecha">
                                <?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?>
                            </td>
                            <td class="tarjeta">
                                •••• <?= htmlspecialchars($compra['num_tarjeta']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer>
</footer>

    <script src="../assets/js/visible.js"></script>
</body>
</html>
