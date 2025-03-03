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
        // Capturar y decodificar los datos recibidos
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Capturar variables
        $valorApertura = $data['valorApertura'];
        $fechaApertura = DateTime::createFromFormat('d/m/Y H:i:s', $data['fechaApertura'])->format('Y-m-d H:i:s');
        $id_sede = $data['idSede']; // Tomar la sede enviada desde el frontend

        // Validar si hay una caja sin cerrar en la sede seleccionada
        $cajaSinCerrar = $configuracion->checkCajaSinCerrar($id_sede);
        if ($cajaSinCerrar) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se puede abrir caja porque hay una caja sin cerrar en esta sede']);
            exit;
        }

        // Abrir caja
        $resultado = $configuracion->abrirCaja($id_user, $valorApertura, $id_sede, $fechaApertura);
        if ($resultado) {
            $_SESSION['id_sede'] = $id_sede; // Actualiza la sesión con la sede seleccionada
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja abierta exitosamente en la sede ' . $id_sede]);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al abrir la caja']);
        }
        break;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}
