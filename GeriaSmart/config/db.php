<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // coloca tu contraseña si aplica
$dbname = 'geriasmart_db';
$port = 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

// Verificar conexión
if(!$conn){
    die('Error de conexión: ' . mysqli_connect_error());
}
?>
