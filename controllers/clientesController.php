<?php
require_once '../models/clientes.php';
$option = (empty($_GET['option'])) ? '' : $_GET['option'];
$clientes = new ClientesModel();
switch ($option) {

    case 'listar':
        $data = $clientes->getClients();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['id_cliente'] = $data[$i]['id_cliente'] ?? '';
            $data[$i]['nombres'] = $data[$i]['nombres'] ?? '';
            $data[$i]['apellidos'] = $data[$i]['apellidos'] ?? '';
            $data[$i]['area'] = $data[$i]['area'] ?? '';
            $data[$i]['sueldo'] = $data[$i]['sueldo'] ?? '';
        }
        echo json_encode($data);
        break;

    case 'listar-clientes':
        $result = $clientes->getClients();
        echo json_encode($result);
        break;

    default:
        echo json_encode(['error' => 'Invalid option']);
        break;
    
} 

