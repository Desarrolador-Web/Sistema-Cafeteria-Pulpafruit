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

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}