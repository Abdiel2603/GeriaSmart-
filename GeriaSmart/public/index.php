<?php
session_start();
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="assets/css/inicio.css">
        <link rel="stylesheet" href="assets/css/inicio_responsive.css">
        <link rel="stylesheet" href="assets/css/animations.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <title>GeriaSmart</title>

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
<section class="hero" id="inicio">
    <div class="container">
        <div class="hero-flex">
            <!-- Columna izquierda-->
            <div class="hero-text-col">
                <h1 class="hero-title reveal" style="font-weight: 700;">Monitoreo inteligente, que cuida al adulto de forma eficiente.</h1>
                <p class="hero-text reveal">
                    GeriaSmart ofrece monitoreo inteligente y asistencia digital superior para el cuidado de personas mayores, utilizando nuestra smartband avanzada y dedicada para los adultos mayores, combina atención humana con tecnología especializada en el cuidado para quienes lo necesitan.
                </p>
                <div class="btns">
                        <a class="btn-primary reveal" href="descargar.php">
                            <span>Descargar</span>
                            <i class="icon bi bi-download"></i>
                        </a>
                        <a class="btn-secondary reveal" href="leermas.php">
                            <span>Leer más</span>
                            <i class="icon bi bi-arrow-right"></i>
                        </a>
                </div>
            </div>
            <!-- Columna derecha -->
            <div class="hero-img-col">
                <img class="hero-main-img reveal" src="assets/img/mockup_1.png"
                     alt="GeriaBand Model 1">
            </div>
        </div>
    </div>
</section>

<section class="funciones" id="funciones">
    <div class="container">
        <h1 class="reveal" style="margin-bottom: 40px;">Innovación que cuida</h1>
        <p class="reveal" style="margin-bottom: 40px">Conoce las funciones que permiten a GeriaSmart aprender, alertar y generar reportes que facilitan el seguimiento y bienestar de los usuarios.</p>
        <div class="funciones-list">
            <article class="funcion reveal">
                <i class="bi bi-bell-fill funcion-icon"></i>
                <h2 style="font-size: 30px;">Alertas Inteligentes</h2>
                <p>La aplicación detecta cambios anormales en los signos vitales y envía alertas inmediatas al cuidador o familiar, garantizando una atención rápida y oportuna ante cualquier situación de riesgo.</p>
            </article>
            <article class="funcion reveal">
                <i class="bi bi-graph-up-arrow funcion-icon"></i>
                <h2 style="font-size: 30px;">Aprendizaje Personalizado</h2>
                <p>La aplicación aprende de las rutinas y hábitos de cada paciente, adaptándose con el tiempo para ofrecer un monitoreo más preciso y sugerencias personalizadas que mejoran su bienestar diario.</p>
            </article>
            <article class="funcion reveal">
                <i class="bi bi-clipboard2-data-fill funcion-icon"></i>
                <h2 style="font-size: 30px;">Reportes e Historial</h2>
                <p>GeriaSmart registra los signos vitales y genera reportes visuales con el historial de salud del usuario, permitiendo a cuidadores y médicos analizar tendencias y tomar mejores decisiones.</p>
            </article>
        </div>
    </div>
</section>

<section class="centrado" id="centrado">
    <div class="container centrado-flex">
        <!-- Columna izquierda-->
        <div class="centrado-text-col">
            <h1 class="reveal">Pensado para todos</h1>
            <p class="reveal">
                Cada detalle de GeriaSmart fue creado para adaptarse a las necesidades de adultos mayores y cuidadores, ofreciendo una experiencia clara, accesible y fácil de comprender. atención humana con tecnología especializada en el cuidado para quienes lo necesitan.
            </p>
        </div>
        <!-- Columna derecha -->
        <div class="centrado-img-col">
            <img class="centrado-img reveal" src="assets/img/mockup_app1.png"
                 alt="GeriaSmart App">
        </div>
    </div>
</section>

<section class="instituciones" id="instituciones">
    <div class="container">
        <h1 class="text-center reveal">Instituciones afiliadas</h1>
        <p class="reveal" style="margin-bottom: 60px">Estas son las instituciones que confían en GeriaSmart, ya sea como parte de su sistema de cuidado, como aliadas en salud preventiva o como organizaciones que avalan nuestra propuesta tecnológica para el bienestar de personas mayores.</p>
        <div class="row reveal g-4 justify-content-center">
            <div class="institucion col-12 col-md-6 col-lg-4">
                <div class="card shadow-lg border-0">
                    <img src="https://lh6.googleusercontent.com/proxy/ZLjT9_aYZy_sA2QgT-WTBKPYPk6hVTO8G4_9pg05YchzRXyRf0glZA38-FRyu6Ou-QirNHF2G8AJ7oeHNELROEtifUGkwlQ22jKRPOX19Xe89zHgoi2N7lY-6vp83w" class="card-img-top" alt="institucion 1">
                    <div class="institucion-label">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h1 style="font-size: 20px;">Residencias Geriátricas Santa María</h1>
                        <div class="icon-location">
                            <p>Estado de México, Naucalpan</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="institucion col-12 col-md-6 col-lg-4">
                <div class="card shadow-lg border-0">
                    <img src="https://oem.com.mx/la-prensa/img/15285796/1661324132/BASE_LANDSCAPE/480/image.webp" class="card-img-top" alt="institucion 2">
                    <div class="institucion-label">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h1>Clínica de Geriatría de SEDESA</h1>
                        <div class="icon-location">
                            <p>Ciudad de México, CDMX</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="institucion card shadow-lg border-0">
                    <img src="https://static.wixstatic.com/media/256729_c5849163e48441f894cfff471f04a19e~mv2.jpg/v1/fill/w_682,h_492,al_c,lg_1,q_80/256729_c5849163e48441f894cfff471f04a19e~mv2.jpg" class="card-img-top" alt="institucion 3">
                    <div class="institucion-label">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h1>Centro Geriatrico Jardin De Los Abuelos</h1>
                        <div class="icon-location">
                            <p>Querétaro, Querétaro</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="institucion card shadow-lg border-0">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6sCN7ZcBG06Jt2fWS8RpQJkANR9cKy9eNNw&s" class="card-img-top" alt="Proveedo 4">
                    <div class="institucion-label">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h1>Residencia Geriátrica Villa San Agustín</h1>
                        <div class="icon-location">
                            <p>Guadalajara, Jalisco</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="institucion card shadow-lg border-0">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOJiw42yBzwU2dWH6icBb_lNHEPm08QM6RyQ&s" class="card-img-top" alt="institucion 5">
                    <div class="institucion-label">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h1>Centro Geriátrico Especializado GERIAGER</h1>
                        <div class="icon-location">
                            <p>Puebla, Puebla</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="institucion card shadow-lg border-0">
                    <img src="https://img.maspormas.com/2023/08/WhatsApp-Image-2023-08-24-at-09.18.47-4-1024x768.jpeg" class="card-img-top" alt="institucion 6">
                    <div class="institucion-label">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h1>Instituto para el Envejecimiento Digno</h1>
                        <div class="icon-location">
                            <p>Ciudad de México, CDMX</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hero-bottom" id="degustaciones">
    <div class="container hero-flex">
        <!-- Columna izquierda-->
        <div class="hero-text-col">
            <h3 class="reveal">Haz la prueba hoy</h3>
            <h1 class="reveal">Visita las tiendas oficiales</h1>
            <p class="hero-text reveal">
                Conoce nuestros puntos de venta autorizados donde podrás probar la GeriaBand, recibir asesoría personalizada y descubrir cómo la tecnología puede mejorar el bienestar de los adultos mayores.
            </p>
            <div class="btns">
                <a href="https://www.google.com/search?sca_esv=ad5d78fe25a26ce1&tbm=lcl&sxsrf=AE3TifPoMAaVW4yt329GrIAoTpFCGVTOHg:1763057455433&q=ver+todas+las+apple+store+de+quer%C3%A9taro&rflfq=1&num=10&sa=X&ved=2ahUKEwjv5OXq3O-QAxUv6ckDHU6uJtQQjGp6BAgkEAE&biw=1526&bih=728&dpr=1.25#rlfi=hd:;si:;mv:[[20.717617699999998,-100.333415],[20.5504855,-100.47006499999999]];tbs:lrf:!1m4!1u3!2m2!3m1!1e1!2m1!1e3!3sIAE,lf:1,lf_ui:4" class="btn-primary reveal">
                    <span>Ver tiendas</span>
                    <i class="icon bi-shop"></i>
                </a>
                <a href="https://www.google.com/maps/search/Apple+Store/@latitud,longitud,14z" class="btn-secondary reveal">
                    <span>Tienda más cercana</span>
                    <i class="icon bi-geo-alt"></i>
                </a>
            </div>
        </div>
        <!-- Columna derecha -->
        <div class="hero-img-col">
            <img class="hero-bottom-img reveal" src="assets/img/icono.png"
                 alt="Icono">
        </div>
    </div>
</section>

<section class="opiniones" id="opiniones">
    <div class="container">
        <h1 class="text-center reveal">Opiniones</h1>
        <p class="reveal" style="margin-bottom: 60px">Conoce lo que opinan quienes ya usan GeriaSmart: desde especialistas en geriatría hasta cuidadores y familiares. Sus experiencias reflejan el impacto real que tiene nuestra tecnología en el cuidado diario de personas mayores.</p>
        <div class="opinion reveal">
            <div class="cliente-col">
                <img class="reveal" src="assets/img/cliente.png" alt="Cliente 1">
                <div class="cliente-titulo reveal">
                    <p>Emir</p>
                    <p>Médico Geriátrico</p>
                </div>
            </div>
            <div class="text-col reveal">
                <p>"Una aplicación competente y de gran ayuda para todos mis pacientes. Me ayuda  a que sus familiares esten al tanto del estado de sus seres queridos, asi como a mi me permite ver mas facilmente las anomalias y signos de riesgos para generar un mejor informe y llegar a un mejor diagnostico."</p>
            </div>
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
                    Política de privacidad
                </a>
                <a class="politic" href="politicasuso.html">
                    Política de uso
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
