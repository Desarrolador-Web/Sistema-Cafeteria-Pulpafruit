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
            $productos = $compras->getProducts($id_caja, $rolUsuario);
            echo json_encode(['productos' => $productos]);
        } else {
            echo json_encode(['error' => 'Faltan parámetros necesarios: id_caja o rolUsuario']);
        }
        break;
    
    case 'buscarProducto':
        if (isset($_GET['barcode'])) {
            $barcode = trim($_GET['barcode']);
            $producto = $compras->buscarProductoPorBarcode($barcode);
            
            if ($producto) {
                echo json_encode([
                    'existe' => true,
                    'precio_compra' => $producto['precio_compra'],
                    'precio_venta' => $producto['precio_venta']
                ]);
            } else {
                echo json_encode(['existe' => false]);
            }
        } else {
            echo json_encode(['error' => 'Código de barras no proporcionado']);
        }
        break;
    
    case 'registrarCompra':
        if (!isset($_SESSION['id_sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede. Por favor abra una caja.']);
            exit;
        }
    
        $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : '';
        $precio_compra = isset($_POST['precio_compra']) ? (float) $_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float) $_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        $sede = isset($_POST['sede']) ? (int) $_POST['sede'] : 0;
        $metodo = isset($_POST['metodo']) ? (int) $_POST['metodo'] : 0;
    
        if (empty($barcode) || empty($precio_compra) || empty($precio_venta) || empty($cantidad) || empty($sede) || empty($metodo)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Todos los campos son obligatorios.']);
            exit;
        }
    
        // Verificar si el producto existe en la sede antes de registrar la compra
        $productoId = $compras->buscarProductoPorBarcodeYSede($barcode, $sede);
    
        if (!$productoId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'El producto que desea comprar no existe en esta sede.']);
            exit; // Detener la ejecución si el producto no existe en la sede
        }
    
        // Registrar la compra
        $compraId = $compras->saveCompra($sede, $precio_compra * $cantidad, $id_user, 1, $sede, $metodo);
    
        if (!$compraId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al registrar la compra.']);
            exit;
        }
    
        // Guardar el detalle de la compra
        $detalleGuardado = $compras->saveDetalle($productoId, $compraId, $cantidad, $precio_compra);
    
        if (!$detalleGuardado) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al guardar el detalle de la compra.']);
            exit;
        }
    
        echo json_encode(['tipo' => 'success', 'mensaje' => 'Compra registrada con éxito.']);
        break;
        
    
    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
