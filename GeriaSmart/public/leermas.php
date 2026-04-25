<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/bootstrap.css">
        <link rel="stylesheet" href="assets/css/inicio.css">
        <link rel="stylesheet" href="assets/css/inicio_responsive.css">
        <link rel="stylesheet" href="assets/css/animations.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <title>GeriaSmart</title>

        <style>
            .fixed-background {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: radial-gradient(circle at left, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0.52) 50%, rgba(255, 255, 255, 0.3) 100%), url("assets/img/hero_background.png");
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                z-index: -1000;
            }

            .centrado {
                background: transparent;
            }

        </style>

        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container navbar-container">
                <!-- Logo a la izquierda -->
                <div class="navbar-section navbar-section-logo">
                    <a href="index.php">
                        <img class="navbar-brand logo" src="assets/img/logo.png" alt="Logo">
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
                            <a class="nav-link navbar-icon-text" href="index.php">
                                <i class="bi bi-house"></i>
                                <span>Inicio</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navbar-icon-text" href="productos.php">
                                <i class="bi bi-box-seam"></i>
                                <span>Productos</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Botón de inicio de sesión a la derecha -->
                    <div class="navbar-section navbar-section-login">
                        <li class="nav-item">
                            <?php if (!isset($_SESSION['id_usr'])): ?>
                                <!-- Sin sesión: muestra Iniciar sesión y manda a login.php -->
                                <a class="btn-void nav-login-btn navbar-icon-text" href="login.php">
                                    <i class="bi bi-person-circle"></i>
                                    <span>Iniciar sesión</span>
                                </a>
                            <?php else: ?>
                            <!-- Con sesión: muestra el nombre y redirige según el tipo de usuario -->
                            <?php if (isset($_SESSION['tipusu']) && $_SESSION['tipusu'] == 1): ?>
                            <a class="btn-void nav-login-btn navbar-icon-text" href="admin/catalogo.php">
                                <?php else: ?>
                                <a class="btn-void nav-login-btn navbar-icon-text" href="cliente/index.php">
                                    <?php endif; ?>
                                    <i class="bi bi-person-circle"></i>
                                    <span><?php echo isset($_SESSION['nom_usr']) ? $_SESSION['nom_usr'] : 'Usuario'; ?></span>
                                </a>
                                <?php endif; ?>
                        </li>
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
            <h1 class="reveal">Más que tecnología: una forma humana de cuidar</h1>
            <p class="reveal">
                GeriaSmart es una solución integral que combina una aplicación móvil intuitiva con una smartband especializada, llamada GeriaBand, diseñada para mejorar el cuidado y la supervisión diaria de personas mayores. Esta tecnología funciona en conjunto: la smartband recopila datos importantes y la aplicación los interpreta, aprende de ellos y actúa según sea necesario.
            </p>
        </div>
        <!-- Columna derecha -->
        <div class="centrado-img-col">
            <img class="centrado-img reveal" src="assets/img/icono_aplicacion.png"
                 alt="GeriaSmart App">
        </div>
    </div>
</section>

<section class="centrado">
    <div class="container centrado-flex">
        <!-- Columna izquierda-->
        <div class="centrado-text-col">
            <h1 class="reveal">Modo de uso de la GeriaBand:</h1>
            <p class="reveal">
                Desde el primer uso, el sistema comienza a monitorear signos vitales en tiempo real, como el ritmo cardíaco, para detectar posibles anomalías. Además, permite establecer recordatorios personalizados de medicamentos, citas o rutinas diarias que se notifican tanto al adulto mayor como a su cuidador si así se configura.
            </p>
        </div>
        <!-- Columna derecha -->
        <div class="centrado-img-col">
            <img class="centrado-img reveal" src="assets/img/icono_corazon.png"
                 alt="GeriaSmart App">
        </div>
    </div>
</section>

<section class="centrado">
    <div class="container centrado-flex">
        <!-- Columna izquierda-->
        <div class="centrado-text-col">
            <h1 class="reveal">Aprende de forma inteligente de cada persona</h1>
            <p class="reveal">
                Una de sus características más valiosas es su capacidad para aprender de las rutinas de cada persona. Con el tiempo, la app identifica patrones de comportamiento, horarios comunes de actividad o descanso, y genera reportes e insights que ayudan a mejorar el cuidado de forma personalizada.
            </p>
        </div>
        <!-- Columna derecha -->
        <div class="centrado-img-col">
            <img class="centrado-img reveal" src="assets/img/icono_cerebro.png"
                 alt="GeriaSmart App">
        </div>
    </div>
</section>

<section class="footer fadeUp">
    <div class="footer-content">
        <div class="logo-col">
            <img src="assets/img/logo.png" alt="Logo">
        </div>
        <div class="contacto-col">
            <p>Contacto</p>
            <div class="contacto-col-info">
                <a class="contacto-icon-text" href="mailto:geriasmart@gmail.com">
                    <i class="bi bi-envelope-fill"></i>
                    <p>geriasmart@gmail.com</p>
                </a>
                <div class="contacto-icon-text">
                    <i class="bi bi-telephone-fill"></i>
                    <p>+52 442 721 6981</p>
                </div>
                <a class="contacto-icon-text" href="https://maps.app.goo.gl/w1oT5y5TXVNHGN8s5">
                    <i class="bi bi-geo-alt-fill"></i>
                    <p>Av. Pie de la Cuesta 2501, Nacional, 76148 Santiago de Querétaro, Qro.</p>
                </a>
            </div>
        </div>
        <div class="politics-col">
            <div class="politics">
                <a class="politic" href="politicasprivacidad.html">
                    Politica de privacidad
                </a>
                <a class="politic" href="politicasuso.html">
                    Politica de uso
                </a>
            </div>
            <div class="footer-redes">
                <p>Siguenos en nuestras redes sociales</p>
                <div class="footer-redes-icons">
                    <a href="https://www.facebook.com/"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.linkedin.com/"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>

    </div>
</section>

<footer>
</footer>

        <script src="assets/js/visible.js"></script>
</body>
</html>
