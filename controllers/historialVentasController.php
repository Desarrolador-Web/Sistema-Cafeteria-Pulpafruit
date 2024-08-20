<?php
require_once '../config.php';
require_once '../models/historialVentas.php';

class HistorialVentasController {
    
    private $model;

    public function __construct() {
        $this->model = new HistorialVentas();
    }

    public function obtenerHistorial() {
        $historial = $this->model->getHistorial();

        // Convertir el número del método de pago a texto legible
        foreach ($historial as &$venta) {
            switch ($venta['metodo']) {
                case 1:
                    $venta['metodo_pago'] = 'Efectivo';
                    break;
                case 2:
                    $venta['metodo_pago'] = 'Bancaria';
                    break;
                case 3:
                    $venta['metodo_pago'] = 'Crédito';
                    break;
                default:
                    $venta['metodo_pago'] = 'Desconocido';
                    break;
            }
        }

        echo json_encode($historial);
    }
}

// Manejo de la solicitud
if (isset($_GET['option']) && $_GET['option'] == 'historial') {
    $controller = new HistorialVentasController();
    $controller->obtenerHistorial();
}
