<?php
$option = isset($_GET['option']) ? $_GET['option'] : '';
require_once '../models/compras.php';
$compras = new Compras();
$id_user = $_SESSION['idusuario'];

switch ($option) {

    case 'listarProductos':
        if (isset($_GET['id_caja']) && isset($_GET['rolUsuario'])) {
            $id_caja = intval($_GET['id_caja']);
            $rolUsuario = intval($_GET['rolUsuario']);
    
            // Obtener productos
            $productos = $compras->getProducts($id_caja, $rolUsuario);
    
            // Crear una única respuesta consolidada
            $respuesta = [
                'parametros_recibidos' => [
                    'id_caja' => $id_caja,
                    'rolUsuario' => $rolUsuario
                ],
                'productos' => $productos
            ];
            echo json_encode($respuesta);
            
        } else {
            echo json_encode(['error' => 'Faltan parámetros necesarios: id_caja o rolUsuario']);
        }
        break;        
    
    case 'listarEmpresas':
        $result = $compras->getEmpresas();
        echo json_encode($result);
        break;
        

    case 'registrarCompra':
        if (!isset($_SESSION['id_sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede. Por favor abra una caja.']);
            exit;
        }
    
        $id_empresa = isset($_POST['id_empresa']) ? (int) $_POST['id_empresa'] : 0;
        $sede = $_SESSION['id_sede'];
        $precio_compra = isset($_POST['precio_compra']) ? (float) $_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float) $_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        $estado = isset($_POST['estado']) ? (int) $_POST['estado'] : 1;
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
        $fecha = date('Y-m-d');
        $imagen = '';
        $metodo_compra = isset($_POST['metodo_compra']) ? (int) $_POST['metodo_compra'] : 0;
    
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
    
        $id_caja = isset($_SESSION['id_sede']) ? (int) $_SESSION['id_sede'] : 0;
    
        if ($id_caja === 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha podido obtener la sede del usuario.']);
            break;
        }
    
        $total = $precio_compra * $cantidad;
    
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
        
    case 'guardarDesdeModal':
        // Verifica si se recibieron los datos necesarios desde el modal
        $sede = isset($_POST['sede']) ? (int) $_POST['sede'] : 0;
        $metodo = isset($_POST['metodo']) ? (int) $_POST['metodo'] : 0;
        $estado = isset($_POST['estado']) ? (int) $_POST['estado'] : null; 
        $id_user = $_SESSION['idusuario'];
    
        // Validar si el estado se recibió correctamente
        if (is_null($estado)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'El estado no fue enviado correctamente.']);
            exit;
        }
    
        // Datos del formulario de productos
        $id_empresa = isset($_POST['id_empresa']) ? (int) $_POST['id_empresa'] : 0;
        $precio_compra = isset($_POST['precio_compra']) ? (float) $_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float) $_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
        $imagen = isset($_FILES['imagen']) ? $_FILES['imagen'] : null;
    
        // Validaciones
        if ($sede === 0 || $metodo === 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Faltan datos del modal (sede o método) para guardar.']);
            exit;
        }
    
        if (empty($id_empresa) || empty($precio_compra) || empty($cantidad) || empty($descripcion)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Faltan datos del producto para guardar la compra.']);
            exit;
        }
    
        // Manejo de la imagen del producto
        $ruta_imagen = '';
        if ($imagen && $imagen['error'] === 0) {
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            $ruta_imagen = 'uploads/' . basename($imagen['name']);
            move_uploaded_file($imagen['tmp_name'], '../' . $ruta_imagen);
        }
    
        // Inicia el flujo de guardado
        $fecha = date('Y-m-d');
    
        $compras->beginTransaction();
    
        try {
            // Guardar la compra
            $total = $precio_compra * $cantidad;
            $id_compra = $compras->saveCompra($id_empresa, $total, $fecha, $id_user, $estado, $sede, $metodo);
    
            if (!$id_compra) {
                throw new Exception('Error al guardar la compra.');
            }
    
            // Guardar el producto
            $id_producto = $compras->saveProduct($barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $ruta_imagen, $cantidad, $estado, $sede);
    
            if (!$id_producto) {
                throw new Exception('Error al guardar el producto.');
            }
    
            // Guardar el detalle de la compra
            $detalle_guardado = $compras->saveDetalle($id_producto, $id_compra, $cantidad, $precio_compra);
    
            if (!$detalle_guardado) {
                throw new Exception('Error al guardar el detalle de la compra.');
            }
    
            $compras->commit();
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Datos del modal guardados con éxito.']);
        } catch (Exception $e) {
            $compras->rollBack();
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
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