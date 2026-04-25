<?php
session_start();
require_once '../../config/db.php';

// --- Verificación de seguridad y autorización ---

// 1. Solo usuarios logueados (cuidador o paciente)
if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

// 2. Debe existir un id_paciente en la URL
if (!isset($_GET['id_paciente'])) {
    header("Location: pacientes_vinculados.php?id_usr={$_SESSION['id_usr']}&error=ID de paciente no especificado.");
    exit;
}

$id_paciente = intval($_GET['id_paciente']);
$id_cuidador = intval($_SESSION['id_usr']);

// 3. Verificar que el paciente esté vinculado AL CUIDADOR que está logueado
// Esto previene que un cuidador edite pacientes de otro.
$sql_verificacion = "SELECT 1 FROM cuidador_paciente WHERE id_paciente = ? AND id_cuidador = ?";
$stmt_verificacion = mysqli_prepare($conn, $sql_verificacion);
mysqli_stmt_bind_param($stmt_verificacion, 'ii', $id_paciente, $id_cuidador);
mysqli_stmt_execute($stmt_verificacion);
$resultado_verificacion = mysqli_stmt_get_result($stmt_verificacion);

if (mysqli_num_rows($resultado_verificacion) === 0) {
    header("Location: pacientes_vinculados.php?id_usr={$id_cuidador}&error=No tienes permiso para editar este paciente.");
    exit;
}
mysqli_stmt_close($stmt_verificacion);


// --- Obtener datos del paciente para el formulario ---

$sql_paciente = "SELECT * FROM paciente WHERE id_paciente = ?";
$stmt_paciente = mysqli_prepare($conn, $sql_paciente);
mysqli_stmt_bind_param($stmt_paciente, 'i', $id_paciente);
mysqli_stmt_execute($stmt_paciente);
$resultado_paciente = mysqli_stmt_get_result($stmt_paciente);

if (mysqli_num_rows($resultado_paciente) === 0) {
    header("Location: pacientes_vinculados.php?id_usr={$id_cuidador}&error=El paciente no existe.");
    exit;
}

$paciente = mysqli_fetch_assoc($resultado_paciente);
mysqli_stmt_close($stmt_paciente);

$error   = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

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
    <title>Editar Paciente</title>

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
            align-items: center;
            padding: 40px 0;
            margin-top: 50px;
        }
        .centrado-flex {
            display: flex;
            flex-direction: column;
            align-items: center;
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
        }
        .form-container {
            align-items: flex-start;
            min-width: 80%;
            max-width: 800px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="fixed-background"></div>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container navbar-container">
            <div class="navbar-section navbar-section-logo">
                <a href="../index.php"><img class="navbar-brand logo" src="../assets/img/logo.png" alt="Logo"></a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link navbar-icon-text" href="./index.php"><i class="bi bi-person"></i><span>Perfil</span></a></li>
                    <li class="nav-item"><a class="nav-link navbar-icon-text" href="mis_compras.php"><i class="bi bi-box-seam"></i><span>Mis compras</span></a></li>
                    <li class="nav-item"><a class="nav-link navbar-icon-text" href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>"><i class="bi bi-person-fill-add"></i><span>Pacientes</span></a></li>
                    <li class="nav-item"><a class="nav-link navbar-icon-text" href="editarinfo.php?id_usr=<?php echo $_SESSION['id_usr']; ?>"><i class="bi bi-gear"></i><span>Configuración</span></a></li>
                </ul>
                <div class="navbar-section navbar-section-login">
                    <a class="btn-void nav-login-btn navbar-icon-text" href="#"><i class="bi bi-person-circle"></i><span><?php echo htmlspecialchars($_SESSION['nom_usr']); ?></span></a>
                </div>
            </div>
        </div>
    </nav>

    <section class="centrado">
        <div class="container centrado-flex">
            <div class="centrado-text-col">
                <h1 class="reveal">Editar Paciente</h1>
                <p class="reveal" style="margin-bottom: 20px;">
                    Actualiza la información del paciente: <?php echo htmlspecialchars($paciente['nom_paciente']); ?>
                </p>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success alert-dismissible fade show reveal" role="alert"><?= htmlspecialchars($mensaje) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show reveal" role="alert"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
            </div>

            <div class="form-container reveal p-4">
                <form class="needs-validation" novalidate action="../../lib/procesar_paciente.php" method="post">
                    <input type="hidden" name="accion" value="editar-paciente">
                    <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <input type="hidden" name="id_cuidador" value="<?php echo $id_cuidador; ?>">
                    <input type="hidden" name="current_pass" value="<?php echo htmlspecialchars($paciente['pass']); ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="nom_paciente" class="form-control" value="<?php echo htmlspecialchars($paciente['nom_paciente']); ?>" required minlength="2" maxlength="255">
                            <div class="invalid-feedback">Ingresa un nombre válido (2-255 caracteres).</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Correo</label>
                            <input type="email" name="mail" class="form-control" value="<?php echo htmlspecialchars($paciente['mail']); ?>" required maxlength="255">
                            <div class="invalid-feedback">Ingresa un correo electrónico válido.</div>
                        </div>

                        
                        
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="pass" class="form-control" value="<?php echo htmlspecialchars($paciente['pass']); ?>" minlength="5" maxlength="20" required>
                            <div class="invalid-feedback">La contraseña debe tener entre 5 y 20 caracteres.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="confirm_pass" class="form-control" minlength="5" maxlength="20">
                            <div class="invalid-feedback">Confirma la contraseña si la modificas.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?>" required>
                            <div class="invalid-feedback">Selecciona una fecha de nacimiento válida.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Género</label>
                            <select name="genero" class="form-select" required>
                                <option value="masculino" <?php echo $paciente['genero'] === 'masculino' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="femenino" <?php echo $paciente['genero'] === 'femenino' ? 'selected' : ''; ?>>Femenino</option>
                            </select>
                            <div class="invalid-feedback">Selecciona el sexo del paciente.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number" name="peso" class="form-control" value="<?php echo htmlspecialchars($paciente['peso']); ?>" required min="1" max="500" step="0.1">
                            <div class="invalid-feedback">Ingresa un peso válido en kilogramos.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estatura (cm)</label>
                            <input type="number" name="estatura" class="form-control" value="<?php echo htmlspecialchars($paciente['estatura']); ?>" required min="30" max="250" step="1">
                            <div class="invalid-feedback">Ingresa una estatura válida en centímetros.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Padecimientos (opcional)</label>
                            <textarea name="padecimientos" class="form-control" rows="3"><?php echo htmlspecialchars($paciente['padecimientos']); ?></textarea>
                        </div>

                        <div class="col-12 mt-4 d-flex justify-content-center gap-2">
                            <button class="btn btn-primary" type="submit">Guardar</button>
                            <a href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>" class="btn-cancel" style="border-color:#FEA2A2 !important;">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="../assets/js/visible.js"></script>
    <script>
    // Script para activar la validación de Bootstrap
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
    </script>
</body>
</html>
