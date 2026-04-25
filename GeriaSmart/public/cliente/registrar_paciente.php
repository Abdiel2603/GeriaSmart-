<?php
session_start();

// Solo cuidadores pueden registrar pacientes
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 3) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$error   = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

// Validar cuidador en URL y que coincida con la sesión
if (!isset($_GET['id_cuidador'])) {
    echo "<script>
            alert('ID de cuidador no reconocido.');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$id_cuidador = intval($_GET['id_cuidador']);

if (!isset($_SESSION['id_usr']) || $id_cuidador !== intval($_SESSION['id_usr'])) {
    header("Location: index.php?error=Solo puedes registrar pacientes para tu propia cuenta.");
    exit;
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
    <title>Registrar paciente</title>

    <style>
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

        .centrado {
            min-height: 100vh;
            position: relative;
            background: transparent;
            display: flex;
            justify-content: center;
            justify-items: center;
            align-items: center;
            padding: 40px 0;
            margin: 50px 0 0 0;
        }

        .centrado-flex {
            display: flex;
            flex-direction: column;
            align-items: center;
            align-content: center;
            justify-content: center;
            justify-items: center;
            width: 90%;
            min-height: 100%;
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
                        <a class="nav-link navbar-icon-text" href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>">
                            <i class="bi bi-person-fill-add"></i>
                            <span>Pacientes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-icon-text" href="editarinfo.php?id_usr=<?php echo $_SESSION['id_usr']; ?>">
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
            <div class="centrado-text-col">
                <h1 class="reveal">Registrar paciente</h1>
                <p class="reveal" style="margin-bottom: 10px;">
                    Ingresa los datos del paciente que deseas vincular a tu cuenta
                </p>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success alert-dismissible fade show reveal">
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show reveal">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="opinion reveal p-4">
                <form class="needs-validation" novalidate action="../../lib/procesar_paciente.php" method="post" style="width: 100% !important;">
                    <input type="hidden" name="accion" value="registrar-paciente">
                    <input type="hidden" name="id_cuidador" value="<?php echo $id_cuidador; ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input type="text"
                                   name="nom_paciente"
                                   class="form-control"
                                   placeholder="Nombre completo"
                                   minlength="2"
                                   maxlength="255"
                                   required>
                            <div class="invalid-feedback">Ingresa un nombre válido (2-255 caracteres).</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Correo</label>
                            <input type="email"
                                   name="mail"
                                   class="form-control"
                                   placeholder="correo@ejemplo.com"
                                   maxlength="255"
                                   required>
                            <div class="invalid-feedback">Ingresa un correo electrónico válido.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password"
                                   name="pass"
                                   class="form-control"
                                   minlength="5"
                                   maxlength="20"
                                   required>
                            <div class="invalid-feedback">La contraseña debe tener entre 5 y 20 caracteres.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password"
                                   name="confirm_pass"
                                   class="form-control"
                                   minlength="5"
                                   maxlength="20"
                                   required>
                            <div class="invalid-feedback">Confirma la contraseña.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date"
                                   name="fecha_nacimiento"
                                   class="form-control"
                                   required>
                            <div class="invalid-feedback">Selecciona una fecha de nacimiento válida.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Género</label>
                            <select name="genero" class="form-select" required>
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                            <div class="invalid-feedback">Selecciona el género del paciente.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number"
                                   name="peso"
                                   class="form-control"
                                   min="1"
                                   max="500"
                                   step="1"
                                   required>
                            <div class="invalid-feedback">Ingresa un peso válido en kilogramos.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estatura (cm)</label>
                            <input type="number"
                                   name="estatura"
                                   class="form-control"
                                   min="30"
                                   max="250"
                                   step="1"
                                   required>
                            <div class="invalid-feedback">Ingresa una estatura válida en centímetros.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Padecimientos (opcional)</label>
                            <textarea
                                name="padecimientos"
                                class="form-control"
                                rows="3"
                                placeholder="Ej. hipertensión, diabetes, etc."></textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <button class="btn btn-primary" type="submit">Registrar</button>
                            <a href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>" class="btn btn-outline-secondary ms-2">Cancelar</a>
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
