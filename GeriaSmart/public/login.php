<?php
session_start();
$mensaje = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje_exito = isset($_GET['msg']) ? $_GET['msg'] : '';
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
        <title>Iniciar sesión</title>

        <style>

            .fixed-background {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: radial-gradient(circle at left, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0.52) 50%, rgba(255, 255, 255, 0.3) 100%), url("./assets/img/login-registro_background.png");
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                z-index: -1000;
            }

            .btn-login {
                width: 80%;
                max-height: 60px;
            }
        </style>

    </head>
    <body class="reveal-static">
        <div class="fixed-background"></div>
        <section class="login" id="login">
            <div class="container">
                <div class="login-card reveal">
                    <form action="../lib/procesar_login.php" method="post">
                        <div class="logo-title">
                            <img src="./assets/img/icono.png" alt="icono" class="logo-title reveal">
                            <h1 class="reveal">Identificarse</h1>
                            <?php
                            if ($mensaje) {
                                ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?= htmlspecialchars($mensaje) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php } ?>
                            <?php
                            if ($mensaje_exito) {
                                ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?= htmlspecialchars($mensaje_exito) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-fields">
                            <div class="form-field reveal">
                                <label>Correo</label>
                                <input type="email" id="mail" name="mail" placeholder="Tu correo electrónico" required>
                            </div>
                            <div class="form-field reveal">
                                <label>Contraseña</label>
                                <input type="password" id="pass" name="pass" placeholder="Tu contraseña" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-login reveal" style="justify-self: center;">
                            Entrar
                            <i class="icon bi bi-box-arrow-in-right"></i>
                        </button>
                        <h3 class="reveal" style="margin-top: 40px;">¿Aun no tienes cuenta?</h3>
                        <a href="registro.php" class="reveal" style="margin-bottom: 10px; color: #2167b9; text-decoration: underline; font-size: 16px; font-weight: bold;">
                            <p>Regístrate</p>
                        </a>
                    </form>
                </div>
            </div>
        </section>

        <footer>
        </footer>

        <script src="./assets/js/visible.js"></script>
    </body>
</html>
