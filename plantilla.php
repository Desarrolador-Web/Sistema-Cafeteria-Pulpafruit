<?php
require_once 'config.php';
require_once 'controllers/plantillaController.php';
$plantilla = new Plantilla();
date_default_timezone_set('America/Bogota'); 

##### PERMISOS #####

require_once 'models/permisos.php';
require_once 'models/admin.php';  // Agregar el modelo admin para verificar la caja abierta
$id_user = $_SESSION['idusuario'];

// Verificar los permisos del usuario
$permisos = new PermisosModel();
$configuracion = $permisos->getPermiso(1, $id_user);
$usuarios = $permisos->getPermiso(2, $id_user);
$clientes = $permisos->getPermiso(3, $id_user);
$productos = $permisos->getPermiso(4, $id_user);
$ventas = $permisos->getPermiso(5, $id_user);
$nueva_venta = $permisos->getPermiso(6, $id_user);
$compras = $permisos->getPermiso(7, $id_user);
$nueva_compra = $permisos->getPermiso(8, $id_user);
$proveedor = $permisos->getPermiso(9, $id_user);

##### FIN PERMISOS #####

// Verificar si el usuario tiene una caja abierta hoy
$admin = new AdminModel();
$fechaHoy = date('Y-m-d');
$cajaAbierta = $admin->checkCajaAbierta($id_user, $fechaHoy);

// Guardar el estado de la caja abierta en la sesión
if ($cajaAbierta) {
    $_SESSION['caja_abierta'] = true;
} else {
    $_SESSION['caja_abierta'] = false;
}

require_once 'views/includes/header.php';
if (isset($_GET['pagina'])) {
    if (empty($_GET['pagina'])) {
        $plantilla->index();
    }else{
        try {
            $archivo = $_GET['pagina'];
            if ($archivo == 'usuarios' && !empty($usuarios)) {
                $plantilla->usuarios();
            } else if ($archivo == 'configuracion' && !empty($configuracion)) {
                $plantilla->configuracion();
            } else if ($archivo == 'clientes' && !empty($clientes)) {
                $plantilla->clientes();
            } else if ($archivo == 'proveedor' && !empty($proveedor)) {
                $plantilla->proveedor();
            }else if ($archivo == 'productos' && !empty($productos)) {
                $plantilla->productos();
            } else if ($archivo == 'ventas' && !empty($nueva_venta)) {
                $plantilla->ventas();
            } else if ($archivo == 'historial' && !empty($ventas)) {                
                $plantilla->historial();
            } else if ($archivo == 'reporte' && !empty($ventas)) {
                $plantilla->reporte();
            } else if ($archivo == 'compras' && !empty($nueva_compra)) {
                $plantilla->compras();
            } else if ($archivo == 'historial_compras' && !empty($ventas)) {
                $plantilla->historial_compras();
            } else if ($archivo == 'reporte_compra' && !empty($compras)) {
                $plantilla->reporte_compra();
            } else{                
                $plantilla->notFound();
            }          
        } catch (\Throwable $th) {            
            $plantilla->notFound();
        }
    }
}else{
    $plantilla->index(); 
}
require_once 'views/includes/footer.php';
