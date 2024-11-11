<?php
require_once '../models/admin.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Cargar el autoloader de Composer

use Postmark\PostmarkClient;

$option = (empty($_GET['option'])) ? '' : $_GET['option'];
$admin = new AdminModel();
$id_user = $_SESSION['idusuario'];




// Configurar la zona horaria a Bogotá, Colombia
date_default_timezone_set('America/Bogota');

switch ($option) {
    case 'verificarCaja':
        $fechaHoy = date('Y-m-d');
        $cajaAbierta = $admin->checkCajaAbierta($id_user, $fechaHoy);
        
        if ($cajaAbierta) {
            $_SESSION['id_sede'] = $cajaAbierta['id_sede'];
            echo json_encode(['cajaAbierta' => true, 'id_sede' => $cajaAbierta['id_sede']]);
        } else {
            $_SESSION['id_sede'] = null;
            echo json_encode(['cajaAbierta' => false]);
        }
        break;

    case 'cerrarCaja':
        if (!isset($_POST['valorCierre'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Valor de cierre es requerido']);
            exit;
        }
    
        $id_usuario = $_SESSION['idusuario'];
        $valorCierre = $_POST['valorCierre'];
        $fechaCierre = date('Y-m-d H:i:s');
    
        $diferencia = $admin->obtenerDiferenciaVentasCompras($id_usuario);
        $resultadoFinal = $diferencia['ResultadoFinal'];
    
        if (floatval($valorCierre) === floatval($resultadoFinal)) {
            $cajaAbierta = $admin->obtenerCajaAbiertaUsuario($id_usuario);
    
            if ($cajaAbierta) {
                $resultado = $admin->cerrarCaja($cajaAbierta['id_info_caja'], $valorCierre, $fechaCierre);
                if ($resultado) {
                    $_SESSION['id_sede'] = null;
                    echo json_encode([
                        'tipo' => 'success',
                        'mensaje' => 'Caja cerrada exitosamente',
                        'resultado' => $resultadoFinal
                    ]);
                } else {
                    echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al cerrar la caja']);
                }
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'No hay caja abierta para cerrar']);
            }
        } else {
            echo json_encode([
                'tipo' => 'success',
                'mensaje' => 'Los valores no coinciden',
                'resultado' => $resultadoFinal
            ]);
        }
        break;

    case 'abrirCaja':
        if (!isset($_POST['valorApertura']) || !isset($_POST['id_sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Campos faltantes en la solicitud']);
            exit;
        }
    
        $valorApertura = $_POST['valorApertura'];
        $id_sede = $_POST['id_sede'];
        $_SESSION['id_sede'] = $id_sede;

        $cajaSinCerrar = $admin->checkCajaSinCerrar($id_sede);

        if ($cajaSinCerrar) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se puede abrir caja porque hay una caja sin cerrar en esta sede']);
            exit;
        }

        $fechaApertura = date('Y-m-d H:i:s');
        $resultado = $admin->abrirCaja($id_user, $valorApertura, $id_sede, $fechaApertura);

        if ($resultado) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja abierta exitosamente']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al abrir la caja']);
        }
        break;

    case 'guardarObservacion':
        // Asegúrate de que el campo 'observacion' e 'id_info_caja' se reciban correctamente
        if (!isset($_POST['observacion']) || !isset($_POST['id_info_caja'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Campos faltantes en la solicitud']);
            exit;
        }

        $observacion = $_POST['observacion'];
        $id_info_caja = $_POST['id_info_caja'];

        // Llama al modelo para guardar la observación
        $resultado = $admin->guardarObservacionYCodigo($id_info_caja, $observacion, null);

        if ($resultado) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Observación guardada exitosamente']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al guardar la observación']);
        }
        break;

    case 'enviarCodigoAutorizacion':
        if (!class_exists('Postmark\PostmarkClient')) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'La clase PostmarkClient no está disponible. Verifique la instalación de la librería.']);
            exit;
        }
    
        try {
            $client = new Postmark\PostmarkClient("22ed804a-3ad8-4752-914c-225acb0c5c26");
    
            $codigoAutorizacion = rand(100000, 999999);
            $emailDestino = "auxdesarrollo@pulpafruit.com";
    
            $sendResult = $client->sendEmail(
                "forgotpass@pulpafruit.com",
                $emailDestino,
                "Autorización cierre de caja con observación",
                "<b>¡Código de Autorización!</b><br><p>Este es el código: <strong>$codigoAutorizacion</strong></p>",
                "¡Código de Autorización!"
            );
    
            if ($sendResult) {
                $tabla = "cf_informacion_cajas";
                $data = ["codigo" => $codigoAutorizacion];
                
                // Instancia del modelo y actualización del código en la base de datos
                $admin = new AdminModel();
                $respuesta = $admin->guardarObservacionYCodigo($_SESSION['id_info_caja'], null, $codigoAutorizacion);
    
                if ($respuesta) {
                    echo json_encode(['tipo' => 'success', 'mensaje' => 'Código enviado exitosamente']);
                } else {
                    echo json_encode(['tipo' => 'error', 'mensaje' => 'No se pudo registrar el código en la base de datos.']);
                }
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al enviar el correo.']);
            }
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Ocurrió un error al enviar el correo: ' . $e->getMessage()]);
        }
        exit;
    
    

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
