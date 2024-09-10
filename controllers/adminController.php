<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/admin.php';  // Asegúrate de que no haya salidas adicionales aquí

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
        // Asegurarse de que la respuesta se envíe como JSON
        header('Content-Type: application/json');

        // Verificar si los valores han sido enviados correctamente
        if (!isset($_POST['valorApertura']) || !isset($_POST['sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Campos faltantes en la solicitud']);
            exit;
        }

        // Obtener los datos del formulario
        $valorApertura = $_POST['valorApertura'];
        $id_sede = $_POST['sede'];

        // Generar la fecha y hora actuales en el servidor
        $fechaApertura = date('Y-m-d H:i:s');

        // Llamar al modelo para guardar la apertura de caja en la base de datos
        $resultado = $admin->abrirCaja($id_user, $valorApertura, $id_sede, $fechaApertura);

        // Enviar la respuesta basada en el resultado
        if ($resultado) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja abierta exitosamente']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al abrir la caja']);
        }

        exit;  // Terminar el script para evitar salidas adicionales
    break;
        
    default:
        break;
}
