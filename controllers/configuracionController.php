<?php
require_once '../models/configuracion.php';

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$option = isset($_GET['option']) ? $_GET['option'] : '';
$configuracion = new ConfiguracionModel();
$id_user = $_SESSION['idusuario'];
date_default_timezone_set('America/Bogota');

switch ($option) {
    case 'abrirCaja':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['valorApertura']) || !isset($data['fechaApertura'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Faltan datos para abrir la caja']);
            exit;
        }

        $valorApertura = $data['valorApertura'];
        $fechaApertura = DateTime::createFromFormat('d/m/Y H:i:s', $data['fechaApertura'])->format('Y-m-d H:i:s');
        $id_sede = $_SESSION['id_sede'] ?? 1; // Asignar sede por defecto si no está en sesión

        // Validar si hay una caja sin cerrar
        $cajaSinCerrar = $configuracion->checkCajaSinCerrar($id_sede);
        if ($cajaSinCerrar) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se puede abrir caja porque hay una caja sin cerrar en esta sede']);
            exit;
        }

        // Abrir caja
        $resultado = $configuracion->abrirCaja($id_user, $valorApertura, $id_sede, $fechaApertura);
        if ($resultado) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja abierta exitosamente']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al abrir la caja']);
        }
        break;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}
