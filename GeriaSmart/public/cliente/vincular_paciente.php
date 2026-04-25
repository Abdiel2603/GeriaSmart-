<?php
session_start();
require_once '../../config/db.php';

// Solo cuidadores pueden vincular pacientes
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 3) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$error = '';

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
    header("Location: index.php?error=Solo puedes vincular pacientes a tu propia cuenta.");
    exit;
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_paciente'])) {
    $id_paciente = intval($_POST['id_paciente']);

    if ($id_paciente <= 0) {
        $error = 'ID de paciente no existente';
    } else {
        // Verificar si el paciente existe
        $sql_check = "SELECT id_paciente FROM paciente WHERE id_paciente = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, 'i', $id_paciente);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            mysqli_stmt_close($stmt_check);

            // Verificar si ya está vinculado con este cuidador
            $sql_ya_vinculado = "SELECT 1 FROM cuidador_paciente WHERE id_cuidador = ? AND id_paciente = ?";
            $stmt_vin = mysqli_prepare($conn, $sql_ya_vinculado);
            mysqli_stmt_bind_param($stmt_vin, 'ii', $id_cuidador, $id_paciente);
            mysqli_stmt_execute($stmt_vin);
            mysqli_stmt_store_result($stmt_vin);
            $ya_vinculado = mysqli_stmt_num_rows($stmt_vin) > 0;
            mysqli_stmt_close($stmt_vin);

            if ($ya_vinculado) {
                $error = 'Paciente ya vinculado a esta cuenta';
            } else {
                // Insertar vinculación
                $sql_vin = "INSERT INTO cuidador_paciente (id_cuidador, id_paciente) VALUES (?, ?)";
                $stmt_vin = mysqli_prepare($conn, $sql_vin);
                mysqli_stmt_bind_param($stmt_vin, 'ii', $id_cuidador, $id_paciente);
                $ok = mysqli_stmt_execute($stmt_vin);
                $errno = mysqli_errno($conn);
                mysqli_stmt_close($stmt_vin);

                if ($ok || $errno == 1062) {
                    header("Location: pacientes_vinculados.php?id_usr=" . $id_cuidador . "&msg=" . urlencode("Vinculado con éxito"));
                    exit;
                }
                $error = 'Error al vincular, intente de nuevo.';
            }
        } else {
            mysqli_stmt_close($stmt_check);
            $error = 'ID de paciente no existente';
        }
    }
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
    <title>Sincronizar paciente</title>

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
            <div class="navbar-section navbar-section-logo">
                <a href="../index.php">
                    <img class="navbar-brand logo" src="../assets/img/logo.png" alt="Logo">
                </a>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
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
                <h1 class="reveal">Sincronizar paciente</h1>
                <p class="reveal" style="margin-bottom: 10px;">
                    Ingresa el ID del paciente que deseas vincular a tu cuenta
                </p>
            </div>

            <div class="opinion reveal p-4">
                <form method="post" action="vincular_paciente.php?id_cuidador=<?php echo $id_cuidador; ?>">
                    <div class="mb-3">
                        <label class="form-label">ID del paciente</label>
                        <input type="number"
                               name="id_paciente"
                               class="form-control"
                               placeholder="Ej. 1, 2, 100..."
                               min="1"
                               required>
                    </div>
                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-3">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Vincular</button>
                    <a href="pacientes_vinculados.php?id_usr=<?php echo $_SESSION['id_usr']; ?>" class="btn btn-outline-secondary ms-2">Cancelar</a>
                </form>
            </div>
        </div>
    </section>

    <footer>
    </footer>

    <script src="../assets/js/visible.js"></script>
</body>
</html>
