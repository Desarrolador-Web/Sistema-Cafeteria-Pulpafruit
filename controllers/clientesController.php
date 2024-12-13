<?php
require_once '../models/clientes.php';

$option = $_GET['option'] ?? '';
$clientesModel = new ClientesModel();

switch ($option) {
    case 'listar':
        try {
            $datos = $clientesModel->listarInformacionCajas();

            // Procesar datos para manejar valores NULL
            $datosProcesados = array_map(function($fila) {
                $fila['fecha_cierre'] = $fila['fecha_cierre'] ?? null; // Convertir NULL a null 
                $fila['valor_cierre'] = $fila['valor_cierre'] ?? null; // Convertir NULL a null 
                return $fila;
            }, $datos);

            echo json_encode(['tipo' => 'success', 'data' => $datosProcesados]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'iniciosSesion':
        try {
            $datos = $clientesModel->getIniciosSesion();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'usuariosRegistrados':
        $data = $clientesModel->getUsuariosRegistrados();
        echo json_encode(['tipo' => 'success', 'data' => $data]);
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