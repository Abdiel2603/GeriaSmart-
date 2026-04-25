<?php
session_start();
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
if (!isset($_SESSION['tipusu']) || $_SESSION['tipusu'] != 1) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$mensaje = "";
$error = "";
$alert_tipo = '';
$alert_texto = '';

if (isset($_GET['msg'])) {
    $alert_tipo  = 'success';
    $alert_texto = $_GET['msg'];
} elseif (isset($_GET['error'])) {
    $alert_tipo  = 'danger';
    $alert_texto = $_GET['error'];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Admin</title>
    <style>
        #eliminarUsuarioModal .modal-content {
            background: #15162f;
            color: #ffffff;
            border: 1px solid #2a2a4a;
        }

        #eliminarUsuarioModal .modal-header,
        #eliminarUsuarioModal .modal-footer {
            border-color: #2a2a4a;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="sidebar-toggle" class="sidebar-checkbox">

    <label for="sidebar-toggle" class="sidebar-toggle">
        <i class="bi bi-list"></i>
    </label>

    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <div class="sidebar">
        <div class="sidebar-header">
            <label for="sidebar-toggle" class="close-sidebar">
                <i class="bi bi-x-lg"></i>
            </label>
            <a href="../index.php">
                <img src="../assets/img/logo.png" alt="Logo">
            </a>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php">
                <i class="bi bi-house-door-fill"></i>
                <span>Inicio</span>
            </a>
            <a href="catalogo.php">
                <i class="bi bi-box-seam-fill"></i>
                <span>Productos</span>
            </a>
            <a href="usuarios.php" class="active">
                <i class="bi bi-people-fill"></i>
                <span>Gestión de usuarios</span>
            </a>
            <a href="../logout.php" style="color: #f61919">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </nav>
    </div>

    <!-- Contenido principal de la página -->
    <div class="main-content">
        <section class="users" id="usuarios">
            <div class="container">
                <h1>Gestionar usuarios</h1>
                <h2>Crea, modifica o elimina usuarios.</h2>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <form action="usuarios.php" method="get" class="d-flex align-items-center">
                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, correo o ID" value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                        <button type="submit" class="btn btn-primary ms-2">Buscar</button>
                        <?php if (isset($_GET['busqueda'])): ?>
                            <a href="usuarios.php" class="btn btn-secondary ms-2">Limpiar</a>
                        <?php endif; ?>
                    </form>
                    <a href="./alta_usuarios.php" class="btn btn-success">Crear nuevo usuario</a>
                </div>
                <h2>Usuarios registrados</h2>
                <?php if (!empty($alert_texto)): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($alert_tipo); ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($alert_texto); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="users-list">
                    <table class="table table-stripped">
                        <thead>
                        <tr>
                            <th >ID de Cuidador</th>
                            <th>Nombre usuario</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        require_once __DIR__ . '/../../lib/gestor_usuarios.php';
                        $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
                        $usuarios = mostrar_usuarios($busqueda);
                        foreach($usuarios as $fila_tabla) {
                            ?>
                            <tr>
                                <td><?= $fila_tabla['id_usr'] ?></td>
                                <td><?= $fila_tabla['nom_usr'] ?></td>
                                <td><?= $fila_tabla['mail'] ?></td>
                                <td><?= $fila_tabla['tel_usr'] ?></td>
                                <td><?php 
                                    switch($fila_tabla['tip_usu']) {
                                        case 1: echo 'Administrador'; break;
                                        case 2: echo 'Paciente'; break;
                                        case 3: echo 'Cuidador'; break;
                                        default: echo 'Desconocido'; break;
                                    }
                                ?></td>
                                <td style="height: 100%; display: flex; flex-direction: column; gap: 30px; margin: 30% 0 10% 0;">
                                    <?php if ($fila_tabla['tip_usu'] != 1): ?>
                                        <a style="box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);" href="editar_usuarios.php?idusr=<?= $fila_tabla['id_usr'] ?>" class="btn-edit btn-warning">Editar</a>
                                        <button
                                            style="box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);"
                                            type="button"
                                            class="btn-delete btn-danger btn-sm btn-eliminar-usuario"
                                            data-id="<?= $fila_tabla['id_usr'] ?>">
                                            Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1" aria-labelledby="eliminarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarUsuarioModalLabel">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que deseas eliminar este usuario?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminarUsuarioBtn">Confirmar eliminación</button>
                </div>
            </div>
        </div>
    </div>

    <form id="eliminarUsuarioForm" action="../../lib/gestor_usuarios.php" method="post" style="display:none;">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id_usr" id="idUsuarioEliminarInput" value="">
    </form>

<footer>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-usuario');
        const modalElement = document.getElementById('eliminarUsuarioModal');
        const confirmarBtn = document.getElementById('confirmarEliminarUsuarioBtn');
        const idInput = document.getElementById('idUsuarioEliminarInput');
        const formEliminar = document.getElementById('eliminarUsuarioForm');
        let idUsuarioSeleccionado = null;

        if (!modalElement || !confirmarBtn || !idInput || !formEliminar) {
            return;
        }

        const modalEliminar = new bootstrap.Modal(modalElement);

        botonesEliminar.forEach(function (boton) {
            boton.addEventListener('click', function () {
                idUsuarioSeleccionado = this.getAttribute('data-id');
                modalEliminar.show();
            });
        });

        confirmarBtn.addEventListener('click', function () {
            if (!idUsuarioSeleccionado) {
                return;
            }
            idInput.value = idUsuarioSeleccionado;
            formEliminar.submit();
        });
    });
</script>
</body>
</html>
