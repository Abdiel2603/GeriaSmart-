<?php
@session_start();

require_once __DIR__ . '/../config/db.php';

function mostrar_productos() {
    global $conn;
    $sql = "SELECT * FROM catalogo";
    $select_preparado = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($select_preparado);

    $resultado = mysqli_stmt_get_result($select_preparado);

    mysqli_stmt_close($select_preparado);
    $productos = array();
    while($fila_bd = mysqli_fetch_assoc($resultado)) {
        $productos[] = $fila_bd;
    }

    return $productos;

}

function agregar_producto($nom_prod, $desc, $prec, $img, $estatus, $stock) {
    global $conn;
    $sql = "INSERT INTO catalogo (nom_prod, `desc`, prec, img, estatus, stock) VALUES (?, ?, ?, ?, ?, ?)";
    $insertar_preparado = mysqli_prepare($conn, $sql);
    if(!$insertar_preparado) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }

    mysqli_stmt_bind_param($insertar_preparado, 'ssssii', $nom_prod, $desc, $prec, $img, $estatus, $stock);

    $query_ok = mysqli_stmt_execute($insertar_preparado);
    $rows_ok = mysqli_affected_rows($conn); // 0 >

    mysqli_stmt_close($insertar_preparado);

    if($query_ok && $rows_ok) {
        return [
            'estatus' => 'msg',
            'mensaje' => 'Producto insertado correctamente'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error al insertar el producto'
        ];
    }
}

function editar_producto($id_prod, $nom_prod, $modelo, $desc, $prec, $img, $estatus, $stock) {
    global $conn;
    $sql = "UPDATE catalogo SET nom_prod = ?, modelo = ?, `desc` = ?, prec = ?, img = ?, estatus = ?, stock = ? WHERE id_prod = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }

    mysqli_stmt_bind_param($stmt, 'sssdsiii', $nom_prod, $modelo, $desc, $prec, $img, $estatus, $stock, $id_prod);
    $query_ok = mysqli_stmt_execute($stmt);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($query_ok && $rows_ok > 0) {
        return [
            'estatus' => 'exitoso',
            'mensaje' => 'Producto actualizado correctamente ✔'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'No se pudo actualizar el producto ❌'
        ];
    }
}


function eliminar_producto($id_prod) {
    global $conn;
    $sql = "DELETE FROM catalogo WHERE id_prod = ?";
    $delete_preparado = mysqli_prepare($conn, $sql); /* Valida que la query este bien y que ya se pueda ejecutar un execute bien */
    if(!$delete_preparado) {
        return [
            'estatus' => '',
            'mensaje' => 'Error en la ejecucion en la base de datos'
        ];
    }

    mysqli_stmt_bind_param($delete_preparado, 'i', $id_prod);
    $query_ok = mysqli_stmt_execute($delete_preparado);
    $rows_ok = mysqli_affected_rows($conn);

    if($query_ok && $rows_ok > 0) {
        return [
            'estatus' => 'exitoso',
            'mensaje' => 'Producto eliminado exitosamente'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'Producto eliminado correctamente'
        ];
    }
}

//    $accion = "agregar";
//    $id_prod = 6;
//    $nom_prod = "Curso de Cobolt";
//    $desc = "Aprende a diseñar, programar y proteger sistemas bancarios con un lenguaje altamente robusto en cuanto a seguridad de información y acciones de este tipo.";
//    $prec = 1000;
//    $img = "https://www.esic.edu/sites/default/files/2024-02/lenguaje%20cobol.jpeg";
//    $estatus = 1;
//    $stock = 50;
//
//    switch($accion){
//        case 'agregar':
//            $resultado = agregar_producto($nom_prod, $desc, $prec, $img, $estatus, $stock);
//            print_r($resultado);
//            break;
//        case 'editar':
//            $resultado = editar_producto($id_prod, $nom_prod, $desc, $prec, $img, $estatus, $stock);
//            print_r($resultado);
//            break;
//        case 'eliminar':
//            $resultado = eliminar_producto($id_prod);
//            print_r($resultado);
//            break;
//        default:
//            echo 'Accion no encontrada, intente de nuevo';
//    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        switch ($accion) {
            case 'agregar':
                if (isset($_POST['nom_prod'], $_POST['desc'], $_POST['prec'], $_POST['img'], $_POST['stock'])) {
                    $nom_prod = trim($_POST['nom_prod']);
                    $desc = trim($_POST['desc']);
                    $prec = (float)$_POST['prec'];
                    $img = trim($_POST['img']);
                    $estatus = 1;
                    $stock = (int)$_POST['stock'];

                    $resultado = agregar_producto($nom_prod, $desc, $prec, $img, $estatus, $stock);
                    print_r($resultado);
                    header('Location: ../public/admin/alta_productos.php?' . $resultado['estatus'] . '=' . $resultado['mensaje']);
                    exit;
                }
                break;

            case 'editar':
                if (isset($_POST['id_prod'], $_POST['nom_prod'], $_POST['desc'], $_POST['prec'], $_POST['img'], $_POST['estatus'], $_POST['stock'])) {
                    $id_prod = intval($_POST['id_prod']);
                    $nom_prod = trim($_POST['nom_prod']);
                    $modelo = trim($_POST['modelo']);
                    $desc = trim($_POST['desc']);
                    $prec = floatval($_POST['prec']);
                    $img = trim($_POST['img']);
                    $estatus = isset($_POST['estatus']) ? intval($_POST['estatus']) : 1;
                    $stock = intval($_POST['stock']);

                    $resultado = editar_producto($id_prod, $nom_prod, $modelo,$desc, $prec, $img, $estatus, $stock);
                    print_r($resultado);
                    header("Location: ../public/admin/catalogo.php?msg=" . urlencode($resultado['mensaje']));
                    exit;
                }
                break;

            case 'eliminar':
                $id_prod   = intval($_POST['id_prod']);
                $resultado = eliminar_producto($id_prod);
                print_r($resultado);
                header("Location: ../admin/catalogo.php?msg=" . urlencode($resultado['mensaje']));
                break;
            default:
                echo 'Accion invalida, prueba otra distinta';

        }
    } else {
        echo "
            <script>
                alert('Accion no detectada, intente de nuevo');
                window.location.href = '../public/admin/catalogo.php';
            </script>
            ";
        exit;
    }
}

?>