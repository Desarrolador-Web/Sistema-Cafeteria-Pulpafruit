<?php
require_once 'conexion.php';

class HistorialVentas {
    
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    public function getHistorial() {
        $query = $this->db->query("SELECT 
                                        v.id_ventas, 
                                        c.nombre AS nombres, 
                                        p.descripcion AS producto, 
                                        CAST(dv.cantidad AS INT) AS cantidad, 
                                        CAST(p.precio_venta AS DECIMAL(10, 2)) AS precio_venta, 
                                        (CAST(dv.cantidad AS INT) * CAST(p.precio_venta AS DECIMAL(10, 2))) AS subtotal, 
                                        v.metodo, 
                                        v.total, 
                                        v.fecha
                                    FROM cf_ventas v
                                    INNER JOIN cf_personal c ON v.id_personal = c.cedula
                                    INNER JOIN cf_detalle_ventas dv ON v.id_ventas = dv.id_ventas
                                    INNER JOIN cf_producto p ON dv.id_producto = p.id_producto
                                    ORDER BY v.fecha DESC;");

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}