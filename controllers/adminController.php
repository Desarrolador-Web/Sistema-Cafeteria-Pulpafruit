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
        
        // Verificar el rol directamente desde la sesión
        $rol = $_SESSION['rol'] ?? null;
    
        if ($rol === 1 || $rol === 2) {
            echo json_encode(['cajaAbierta' => true]); // No mostrar el modal para roles 1 y 2
        } elseif ($cajaAbierta) {
            $_SESSION['id_sede'] = $cajaAbierta['id_sede'];
            echo json_encode(['cajaAbierta' => true, 'id_sede' => $cajaAbierta['id_sede']]);
        } else {
            $_SESSION['id_sede'] = null;
            echo json_encode(['cajaAbierta' => false]);
        }
        break;

    case 'validarCodigoAutorizacion':
        if (!isset($_POST['id_info_caja']) || !isset($_POST['codigoAutorizacion']) || !isset($_POST['valorCierre'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Campos faltantes en la solicitud']);
            exit;
        }
    
        $id_info_caja = $_POST['id_info_caja'];
        $codigoIngresado = $_POST['codigoAutorizacion'];
        $valorCierre = $_POST['valorCierre'];
        $fechaCierre = date('Y-m-d H:i:s');
    
        // Validar el código de autorización en la base de datos
        if ($admin->validarCodigo($id_info_caja, $codigoIngresado)) {
            // Código correcto: Cerrar caja actualizando valor_cierre y fecha_cierre
            $resultado = $admin->cerrarCaja($id_info_caja, $valorCierre, $fechaCierre);
            if ($resultado) {
                echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja cerrada exitosamente']);
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al cerrar la caja en la base de datos']);
            }
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Código incorrecto']);
        }
        break;
        

    case 'obtenerIdCajaAbierta':
        $id_usuario = $_SESSION['idusuario'];
        $fechaHoy = date('Y-m-d');
        $cajaAbierta = $admin->checkCajaAbierta($id_usuario, $fechaHoy);
    
        if ($cajaAbierta) {
            echo json_encode(['id_info_caja' => $cajaAbierta['id_info_caja']]);
        } else {
            echo json_encode(['id_info_caja' => null]);
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
        if (!isset($_POST['id_info_caja'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'ID de caja no especificado']);
            exit;
        }
    
        $id_info_caja = $_POST['id_info_caja'];
        $codigoAutorizacion = rand(100000, 999999);  // Genera un código aleatorio de 6 dígitos
    
        // Guardar el código de autorización en la base de datos
        $resultado = $admin->guardarObservacionYCodigo($id_info_caja, null, $codigoAutorizacion);
    
        if ($resultado) {
            // Enviar el código por correo (suponiendo que el envío por correo está configurado aquí)
            $client = new PostmarkClient("22ed804a-3ad8-4752-914c-225acb0c5c26");
            $emailDestino = "auxdesarrollo@pulpafruit.com";
            $sendResult = $client->sendEmail(
                "forgotpass@pulpafruit.com",
                $emailDestino,
                "Autorización cierre de caja",
                "<b>¡Código de Autorización!</b><br><p>Este es el código: <strong>$codigoAutorizacion</strong></p>",
                "¡Código de Autorización!"
            );
    
            if ($sendResult) {
                echo json_encode(['tipo' => 'success', 'mensaje' => 'Código enviado exitosamente']);
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al enviar el correo']);
            }
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se pudo registrar el código en la base de datos']);
        }
        break;
        

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
