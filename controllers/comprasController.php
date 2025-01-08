<?php
$option = isset($_GET['option']) ? $_GET['option'] : '';
require_once '../models/compras.php';
$compras = new Compras();
$id_user = $_SESSION['idusuario'];

switch ($option) {

    case 'listarProductos':
        // Verifica si el valor de id_sede está en la sesión
        if (isset($_SESSION['id_sede'])) {
            $id_caja = $_SESSION['id_sede'];
            $result = $compras->getProducts($id_caja);
            echo json_encode($result);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede.']);
        }
        break;
    
    case 'listarEmpresas':
        $result = $compras->getEmpresas();
        echo json_encode($result);
        break;
        

    case 'registrarCompra':
        // Verifica si hay un id_sede en la sesión
        if (!isset($_SESSION['id_sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede. Por favor abra una caja.']);
            exit;
        }
    
        $id_empresa = isset($_POST['id_empresa']) ? (int) $_POST['id_empresa'] : 0;
        $sede = $_SESSION['id_sede'];  // Utiliza el id_sede de la sesión
        $precio_compra = isset($_POST['precio_compra']) ? (float) $_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float) $_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        $estado = isset($_POST['estado']) ? (int) $_POST['estado'] : 1;
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
        $fecha = date('Y-m-d');
        $imagen = '';
        $metodo_compra = isset($_POST['metodo_compra']) ? (int) $_POST['metodo_compra'] : 0; // Método de compra
    
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
    
        // Obtener el número de sede de la sesión
        $id_caja = isset($_SESSION['id_sede']) ? (int) $_SESSION['id_sede'] : 0;
    
        if ($id_caja === 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha podido obtener la sede del usuario.']);
            break;
        }
    
        $total = $precio_compra * $cantidad;
        // Asegúrate de almacenar el método de compra en la base de datos
        $compraId = $compras->saveCompra($id_empresa, $total, $fecha, $id_user, $estado, $id_caja, $metodo_compra);
    
        if (!$compraId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al registrar la compra.']);
            break;
        }
    
        $productId = $compras->saveProduct($barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja);
    
        if (!$productId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al guardar el producto.']);
            break;
        }
    
        $result = $compras->saveDetalle($productId, $compraId, $cantidad, $precio_compra);
    
        if (!$result) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al guardar el detalle de la compra.']);
            break;
        }
    
        echo json_encode(['tipo' => 'success', 'mensaje' => 'Compra registrada con éxito.']);
        break;
    

    case 'cambiarEstado':
        $id_producto = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $estado = isset($_POST['estado']) ? (int) $_POST['estado'] : 1;
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
    
        if (empty($id_producto) || empty($barcode)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Datos incompletos para cambiar el estado.']);
            exit;
        }
    
        // Actualizar el estado del producto y agregar el código de barras si estaba vacío
        $result = $compras->updateEstadoProducto($id_producto, $estado, $barcode);
    
        if ($result) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Estado del producto actualizado con éxito.']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al actualizar el estado del producto.']);
        }
        exit;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}