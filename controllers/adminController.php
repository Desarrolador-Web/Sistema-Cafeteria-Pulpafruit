<?php
require_once '../models/admin.php';
$option = (empty($_GET['option'])) ? '' : $_GET['option'];
$admin = new AdminModel();
$id_user = $_SESSION['idusuario'];

switch ($option) {
    case 'verificarCaja':
        // Verificar si la caja ya está abierta para el usuario y la fecha actual
        $fechaHoy = date('Y-m-d');
        $cajaAbierta = $admin->checkCajaAbierta($id_user, $fechaHoy);
        echo json_encode(['cajaAbierta' => $cajaAbierta]);
        break;

    case 'abrirCaja':
        // Verificar si los valores han sido enviados correctamente
        if (!isset($_POST['valorApertura']) || !isset($_POST['id_sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Campos faltantes en la solicitud']);
            exit;
        }

        // Obtener los datos del formulario
        $valorApertura = $_POST['valorApertura'];
        $id_sede = $_POST['id_sede'];

        // Generar la fecha y hora actuales
        $fechaApertura = date('Y-m-d H:i:s');

        // Guardar la apertura de caja en la base de datos
        $resultado = $admin->abrirCaja($id_user, $valorApertura, $id_sede, $fechaApertura);

        if ($resultado) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja abierta exitosamente']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al abrir la caja']);
        }
        break;

    default:
        break;
}
