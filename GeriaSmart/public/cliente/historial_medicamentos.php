<?php
session_start();
require_once '../../config/db.php';
require_once '../../lib/gestor_pacientes.php';

// --- Validación de Acceso ---
if (!isset($_SESSION['tipusu']) || ($_SESSION['tipusu'] != 2 && $_SESSION['tipusu'] != 3)) {
    header("Location: ../login.php?error=Acceso no autorizado");
    exit;
}

$id_paciente = isset($_GET['id_paciente']) ? intval($_GET['id_paciente']) : 0;
if ($id_paciente === 0) {
    header("Location: pacientes_vinculados.php?id_usr=" . $_SESSION['id_usr'] . "&error=Paciente no especificado");
    exit;
}

// --- Obtener datos del paciente ---
$paciente = obtener_paciente_por_id($conn, $id_paciente);

// --- Consulta (Sin cambios en columnas) ---
$sql = "SELECT 
            id_medicamento,
            nombre,
            dosis,
            frecuencia_horas,
            cantidad_total,
            cantidad_restante,
            dias_tratamiento
        FROM medicamento 
        WHERE id_paciente = ?
        ORDER BY id_medicamento DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_paciente);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$medicamentos = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeriaSmart - Medicamentos</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Fondo igual a pacientes_vinculados */
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
    margin: 50px 0 0 0;
}

.centrado-flex {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 90%;
}

.centrado-text-col {
    z-index: 2;
    margin: 0 auto;
    max-width: 1000px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.tabla-pacientes {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-top: 20px;
}

.tabla-pacientes th {
    background: var(--green);
    color: #ffffff;
    padding: 15px;
    text-align: left;
    font-weight: bold;
}

.tabla-pacientes td {
    padding: 15px;
    border-bottom: 1px solid #eeeeee;
}

.tabla-pacientes tr:last-child td {
    border-bottom: none;
}

.tabla-pacientes tr:hover {
    background: #f9f9f9;
}

.med-nombre{
    font-weight:600;
    color:#333;
}

.med-detalle{
    font-size:13px;
    color:#777;
}

.badge-status{
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:600;
}

.badge-proceso{
    background:#e8f9f1;
    color:#1f7a4d;
}

.badge-terminado{
    background:#fdeaea;
    color:#a11;
}
    </style>
</head>
<body>

<div class="fixed-background"></div>

<section class="centrado">
<div class="container centrado-flex">

<div class="centrado-text-col">

<h1>Historial Completo de Medicamentos</h1>
            <h2><?= htmlspecialchars($paciente['nom_paciente']) ?></h2>
            <a href="historial.php?id_paciente=<?= $id_paciente ?>" class="btn btn-light mt-2">
                <i class="bi bi-arrow-left"></i> Volver al Historial Principal
            </a>

</div>


<div class="reveal p-4">

<?php if (empty($medicamentos)): ?>

<h2>No hay medicamentos registrados</h2>
<p>Cuando agregues medicamentos aparecerán aquí.</p>

<?php else: ?>

<table class="tabla-pacientes">

<thead>
<tr>
<th>Medicamento</th>
<th>Dosis</th>
<th>Inventario</th>
<th>Tratamiento</th>
<th>Estado</th>
</tr>
</thead>

<tbody>

<?php foreach ($medicamentos as $med): 
$es_terminado = ($med['cantidad_restante'] <= 0);
?>

<tr>

<td>
<div class="med-nombre"><?= htmlspecialchars($med['nombre']) ?></div>
<p class="med-detalle">ID: <?= $med['id_medicamento'] ?></p>
</td>

<td>
<p class="med-detalle"><?= htmlspecialchars($med['dosis']) ?></p>
<p class="med-detalle">Cada <?= $med['frecuencia_horas'] ?> horas</p>
</td>

<td>
<p class="med-detalle">
<?= $med['cantidad_restante'] ?> / <?= $med['cantidad_total'] ?> unidades
</p>
</td>

<td>
<p class="med-detalle">
<?= $med['dias_tratamiento'] ?> días
</p>
</td>

<td>

<?php if ($es_terminado): ?>
<span class="badge-status badge-terminado">Terminado</span>
<?php else: ?>
<span class="badge-status badge-proceso">En proceso</span>
<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

<?php endif; ?>

</div>
</div>
</section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>