<?php
$option = (empty($_GET['option'])) ? '' : $_GET['option'];
require_once '../models/historialCompras.php';
$compras = new Compras();

switch ($option) {
    case 'historial':
        $result = $compras->getHistorialComprasDespachadas();
        echo json_encode($result);
        break;

    default:
        echo json_encode([]);
        break;
}
