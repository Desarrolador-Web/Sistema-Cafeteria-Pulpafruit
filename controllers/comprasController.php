<?php
$option = isset($_GET['option']) ? $_GET['option'] : '';
require_once '../models/compras.php';
$compras = new Compras();
$id_user = $_SESSION['idusuario']; 

switch ($option) {
    case 'listarProductos':
        $result = $compras->getProducts();
        echo json_encode($result);
        break;

    case 'listarEmpresas':
        $result = $compras->getEmpresas();
        echo json_encode($result);
        break;
        

    case 'registrarCompra':
        $id_empresa = isset($_POST['id_empresa']) ? (int)$_POST['id_empresa'] : 0;
        $precio_compra = isset($_POST['precio_compra']) ? (float)$_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float)$_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
        $fecha = date('Y-m-d');
        $imagen = '';

        if (empty($id_empresa)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Por favor seleccione una empresa.']);
            break;
        }

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
            move_uploaded_file($_FILES['imagen']['tmp_name'], '../' . $imagen);
        }

        // Guardar la compra
        $total = $precio_compra * $cantidad;
        $compraId = $compras->saveCompra($id_empresa, $total, $fecha, $id_user, $estado);

        if (!$compraId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al registrar la compra.']);
            break;
        }

        // Insertar nuevo producto
        $productId = $compras->saveProduct($barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado);

        // Guardar el detalle de la compra
        $compras->saveDetalle($productId, $compraId, $cantidad, $precio_compra);

        echo json_encode(['tipo' => 'success', 'mensaje' => 'Compra registrada con éxito.']);
        break;
        

    case 'cambiarEstado':
        $id_producto = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';

        if (empty($id_producto) || empty($barcode)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Datos incompletos para cambiar el estado.']);
            break;
        }

        $result = $compras->updateEstadoProducto($id_producto, $estado, $barcode);

        if ($result) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Estado del producto actualizado con éxito.']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al actualizar el estado del producto.']);
        }
        break;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
?>
