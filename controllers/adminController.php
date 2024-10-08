<?php
require_once '../models/admin.php';
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
            // Si el usuario tiene una caja abierta, guarda el id_sede en la sesión
            $_SESSION['id_sede'] = $cajaAbierta['id_sede'];
            echo json_encode(['cajaAbierta' => true, 'id_sede' => $cajaAbierta['id_sede']]);
        } else {
            // Si no hay caja abierta, borra el id_sede de la sesión
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
    
        // Obtener la diferencia de ventas y compras usando la consulta proporcionada
        $diferencia = $admin->obtenerDiferenciaVentasCompras($id_usuario);
        $resultadoFinal = $diferencia['ResultadoFinal'];
    
        // Verificar si el valor de cierre coincide con el resultado de la consulta
        if (floatval($valorCierre) === floatval($resultadoFinal)) {
            // Cerrar caja normalmente
            $cajaAbierta = $admin->obtenerCajaAbiertaUsuario($id_usuario);
    
            if ($cajaAbierta) {
                $resultado = $admin->cerrarCaja($cajaAbierta['id_info_caja'], $valorCierre, $fechaCierre);
                if ($resultado) {
                    $_SESSION['id_sede'] = null; // Limpiar id_sede después de cerrar la caja
                    echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja cerrada exitosamente']);
                } else {
                    echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al cerrar la caja']);
                }
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'No hay caja abierta para cerrar']);
            }
        } else {
            // Los valores no coinciden, devolver el resultado de la consulta
            echo json_encode([
                'tipo' => 'success', // Tipo success para que siga el flujo en el JS
                'mensaje' => 'Los valores no coinciden',
                'resultado' => $resultadoFinal // Valor de la diferencia de ventas y compras
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

        // Guardar el número de sede en la sesión para futuras referencias
        $_SESSION['id_sede'] = $id_sede;

        // Verificar si ya existe una caja abierta sin cerrar en la misma sede
        $cajaSinCerrar = $admin->checkCajaSinCerrar($id_sede);

        if ($cajaSinCerrar) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se puede abrir caja porque hay una caja sin cerrar en esta sede']);
            exit;
        }

        // Guardar la caja
        $fechaApertura = date('Y-m-d H:i:s');
        $resultado = $admin->abrirCaja($id_user, $valorApertura, $id_sede, $fechaApertura);

        if ($resultado) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Caja abierta exitosamente']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al abrir la caja']);
        }
        break;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
