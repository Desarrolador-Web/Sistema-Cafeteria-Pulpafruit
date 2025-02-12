<?php
require_once '../models/clientes.php';

$option = $_GET['option'] ?? '';
$clientesModel = new ClientesModel();

switch ($_GET['option']) {
    case 'iniciosSesion':
        try {
            $datos = $clientesModel->getIniciosSesion();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'usuariosRegistrados':
        try {
            $datos = $clientesModel->getUsuariosRegistrados();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'productosAgotados':
        try {
            $datos = $clientesModel->getProductosAgotados();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'nivelarInventario':
        try {
            $datos = $clientesModel->getNivelarInventario();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;
        
    case 'actualizarProducto':
        try {
            // Obtener los valores enviados desde el frontend
            $codigo_producto = $_POST['codigo_producto'];
            $existencia = $_POST['existencia'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
    
            // Llamar al modelo para actualizar los datos
            $resultado = $clientesModel->actualizarProducto($codigo_producto, $existencia, $precio_compra, $precio_venta);
            
            if ($resultado) {
                echo json_encode(['tipo' => 'success', 'mensaje' => 'Producto actualizado correctamente']);
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el producto']);
            }
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}