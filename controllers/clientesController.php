<?php
require_once '../models/clientes.php';

$option = $_GET['option'] ?? '';
$clientesModel = new ClientesModel();

switch ($option) {
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
            $codigo_producto = $_POST['codigo_producto'];
            $existencia = $_POST['existencia'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
            $confirmacion = $_POST['confirmacion'] ?? false;
    
            // Llamar al modelo y pasar la confirmación
            $resultado = $clientesModel->actualizarProducto($codigo_producto, $existencia, $precio_compra, $precio_venta, $confirmacion);
    
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'descargarInforme':
        try {
            $datos = $clientesModel->getDescargarInforme();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}