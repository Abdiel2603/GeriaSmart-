<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_usr         = trim($_POST['name'] ?? '');
    $mail            = trim($_POST['email'] ?? '');
    $tel_usr         = trim($_POST['phone'] ?? '');
    $pass            = trim($_POST['password'] ?? '');
    $confirmpassword = trim($_POST['confirmpassword'] ?? '');

    if (empty($nom_usr) || empty($mail) || empty($tel_usr) || empty($pass) || empty($confirmpassword)) {
        $response['message'] = 'Todos los campos son obligatorios.';
    } elseif (!preg_match('/^[0-9]{10}$/', $tel_usr)) {
        $response['message'] = 'El teléfono debe contener exactamente 10 dígitos.';
    } elseif ($pass !== $confirmpassword) {
        $response['message'] = 'Las contraseñas no coinciden.';
    } else {
            /* NUEVO: verificar si el correo ya existe */
            $sqlCheck = "SELECT id_usr FROM usuario WHERE mail = ?";
            $stmtCheck = $conn->prepare($sqlCheck);

            if ($stmtCheck) {
                $stmtCheck->bind_param("s", $mail);
                $stmtCheck->execute();
                $stmtCheck->store_result();

                if ($stmtCheck->num_rows > 0) {
                    // Ya hay un usuario con ese correo
                    $response['message'] = 'El correo electrónico ya está registrado.';
                    $stmtCheck->close();
                } else {
                    $stmtCheck->close();

                    // Insertar usuario normal con teléfono
                    $sql = "INSERT INTO usuario (nom_usr, mail, pass, tel_usr, tip_usu) VALUES (?, ?, ?, ?, 3)";
                    try {
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssss", $nom_usr, $mail, $pass, $tel_usr);
                        $stmt->execute();

                        $response['success'] = true;
                        $response['message'] = 'Usuario registrado exitosamente';
                        header('Location: login.php?msg=' . urlencode($response['message']));
                        exit;
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            $response['message'] = 'El correo electrónico ya está registrado.';
                        } else {
                            $response['message'] = 'Ocurrió un error inesperado al intentar registrarte: ' . $e->getMessage();
                        }
                    }
                }
            } else {
                $response['message'] = 'Error al verificar el correo electrónico.';
            }
        }
    }

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
    <title>Registro</title>
    <style>
        .fixed-background {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at left, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0.52) 50%, rgba(255, 255, 255, 0.3) 100%), url("./assets/img/login-registro_background.png");
            background-size: cover; background-repeat: no-repeat; background-position: center; z-index: -1000;
        }
        .btn-login {
            width: 80%; max-height: 60px;
        }

        .alert-message {
            padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center;
            font-weight: bold;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    </style>
</head>
<body class="no-scroll-reveal">
<div class="fixed-background"></div>
<section class="login" id="login">
    <div class="container">
        <div class="login-card reveal-static">
            <div class="logo-title">
                <h1 class="reveal">Registrarse</h1>
            </div>

            <?php if (!empty($response['message'])): ?>
                <div class="alert-message <?php echo $response['success'] ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo htmlspecialchars($response['message']); ?>
                </div>
                <?php
                // Si el registro fue exitoso, redirigir después de mostrar el mensaje brevemente
                if ($response['success']) {
                    echo '<script>setTimeout(function(){ window.location.href = "login.html"; }, 3000);</script>';
                }
                ?>
            <?php endif; ?>

            <form id="formRegistro" class="form-fields" method="POST">
                <div class="form-field reveal">
                    <label class="form-label">Nombre completo</label>
                    <input class="form-control" type="text" placeholder="Tu nombre" id="name" name="name" required value="<?php echo $_POST['name'] ?? ''; ?>">
                </div>
                <div class="form-field reveal">
                    <label class="form-label">Correo</label>
                    <input class="form-control" type="email" placeholder="Tu correo electrónico" id="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                <div class="form-field reveal">
                    <label class="form-label">Teléfono</label>
                    <input class="form-control" type="tel" placeholder="10 dígitos" id="phone" name="phone" required maxlength="10" pattern="[0-9]{10}" value="<?php echo $_POST['phone'] ?? ''; ?>">
                </div>
                <div class="form-field reveal">
                    <label class="form-label">Contraseña</label>
                    <input class="form-control" type="password" placeholder="Tu contraseña" id="password" name="password" required>
                </div>
                <div class="form-field reveal">
                    <label class="form-label">Confirmar contraseña</label>
                    <input class="form-control" type="password" placeholder="Confirmar tu contraseña" id="confirmpassword" name="confirmpassword" required>
                </div>
                <div class="form-field reveal">
                    <button type="submit" class="btn-login">
                        <span>Registrarse</span>
                        <i class="icon bi bi-person-plus-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<footer>
</footer>
<script src="./assets/js/transicion.js"></script>
<script src="./assets/js/visible.js"></script>
</body>
</html>