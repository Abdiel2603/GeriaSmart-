<?php
session_start();

if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

if(!isset($_GET['id_usr'])) {
    echo "<script>
            alert('ID de usuario no reconocido.');
            window.location.href = 'index.php';
          </script>";
    exit;
} else {
    $id_usr = intval($_GET['id_usr']);
    
    // Verificar que el usuario solo pueda editar su propia información
    if (!isset($_SESSION['id_usr']) || $id_usr !== intval($_SESSION['id_usr'])) {
        header("Location: index.php?error=Solo puedes editar tu propio perfil.");
        exit;
    }
    
    require_once '../../config/db.php';

    $sql  = "SELECT nom_usr, mail, pass, tel_usr, tip_usu FROM usuario WHERE id_usr = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_usr);
    mysqli_stmt_execute($stmt);

    $resultado   = mysqli_stmt_get_result($stmt);
    $numero_rows = mysqli_num_rows($resultado);

    if (!$resultado || $numero_rows === 0) {
        echo "<script>
                alert('No existe un usuario con ese ID.');
                window.location.href = 'usuarios.php';
              </script>";
        exit;
    }

    //Aquí se asocian los datos en vez de usar un arreglo asociativo
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}

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

        .opinion {
            align-items: flex-start;
            min-width: 80%;
            background-color: #ffffff;
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
                        <a class="nav-link navbar-icon-text" href="#">
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
            <h1 class="reveal">Editar información</h1>
            <p class="reveal" style="margin-bottom: 10px;">
                Actualiza tu información de usuario
            </p>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
        <div class="opinion reveal">
            <form id="usuariosForm" class="needs-validation border rounded p-3 bg-white w-100" novalidate action="../../lib/gestor_usuarios.php" method="post" style="width: 100% !important;">

                <input type="hidden" name="id_usr" value="<?= $id_usr ?>">
                <input type="hidden" name="current_pass" value="<?= htmlspecialchars($usuario['pass']) ?>">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                               id="nom_usr"
                               name="nom_usr"
                               class="form-control"
                               placeholder="Ej. Jonh"
                               minlength="2"
                               maxlength="100"
                               value="<?= htmlspecialchars($usuario['nom_usr']) ?>"
                               style="width: 100% !important;"
                               required>
                        <div class="valid-feedback">Nombre válido.</div>
                        <div class="invalid-feedback">Nombre no válido, usa 2-100 caracteres.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Correo</label>
                        <input type="email"
                               name="mail"
                               id="mail"
                               class="form-control"
                               placeholder="Ej. correo@ejemplo.com"
                               value="<?= htmlspecialchars($usuario['mail']) ?>"
                               style="width: 100% !important;"
                               required>
                        <div class="valid-feedback">Correo válido.</div>
                        <div class="invalid-feedback">Correo no válido.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Contraseña</label>
                        <input type="password"
                               class="form-control"
                               id="pass"
                               name="pass"
                               placeholder="asd123!"
                               minlength="5"
                               maxlength="20"
                               value="<?= htmlspecialchars($usuario['pass']) ?>"
                               style="width: 100% !important;"
                               required>
                        <div class="valid-feedback">Contraseña válida.</div>
                        <div class="invalid-feedback">Contraseña no válida, usa 5-20 caracteres.</div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password"
                               class="form-control"
                               id="confirmpassword"
                               name="confirmpassword"
                               placeholder="Confirma tu nueva contraseña"
                               minlength="5"
                               maxlength="20"
                               style="width: 100% !important;"
                               >
                        <div class="valid-feedback">Confirmación válida.</div>
                        <div class="invalid-feedback">Confirma tu contraseña si la modificas.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Teléfono</label>
                        <input id="tel_usr"
                               name="tel_usr"
                               type="tel"
                               class="form-control"
                               placeholder="Ej. 1234567890"
                               value="<?= htmlspecialchars($usuario['tel_usr']) ?>"
                               style="width: 100% !important;">
                        <div class="valid-feedback">Teléfono válido.</div>
                        <div class="invalid-feedback">Máximo 10 digitos.</div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <button class="btn btn-primary" type="submit" name="accion" value="editar-usuario">Guardar</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<footer>
</footer>

    <script src="../assets/js/visible.js"></script>
</body>
</html>
