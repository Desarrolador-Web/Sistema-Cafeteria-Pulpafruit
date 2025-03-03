<?php
$option = (empty($_GET['option'])) ? '' : $_GET['option'];
require_once '../models/historialCompras.php';
$compras = new Compras();

switch ($option) {
    case 'historial':
        $id_sede = $_POST['id_sede'] ?? null;
        $id_usuario = $_POST['id_usuario'] ?? null;
    
        $result = $compras->getHistorialComprasDespachadas($id_sede, $id_usuario);
        echo json_encode($result);
        break;
    

    default:
        echo json_encode([]);
        break;
}
