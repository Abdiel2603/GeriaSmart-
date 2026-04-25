<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_prod'])) {
    header("Location: productos.php");
    exit;
}

$id_prod = intval($_POST['id_prod']);

// Validar que el producto existe y tiene stock
$sql_check = "SELECT id_prod, nom_prod, stock FROM catalogo WHERE id_prod = ? AND estatus = 1";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt, 'i', $id_prod);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if (mysqli_num_rows($resultado) === 0) {
    header("Location: ../public/productos.php?error=Producto no válido");
    exit;
}

$producto = mysqli_fetch_assoc($resultado);
if ($producto['stock'] <= 0) {
    header("Location: ../public/productos.php?error=Producto sin stock");
    exit;
}

// Si ya está logeado, ir directo al formulario de tarjeta
if (isset($_SESSION['id_usr'])) {
    header("Location: ../public/formulario_tarjeta.php?id_prod=" . $id_prod);
    exit;
} else {
    // Guardar en sesión y redirigir a login
    $_SESSION['id_prod_comprar'] = $id_prod;
    header("Location: ../public/login.php?redirect=compra");
    exit;
}
?>