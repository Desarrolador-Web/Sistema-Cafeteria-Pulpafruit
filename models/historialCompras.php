<?php
require_once '../config.php';
require_once 'conexion.php';

class Compras {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getHistorialComprasDespachadas($id_sede, $id_usuario) {
        $query = "
            SELECT 
                c.id_compra AS id,
                e.razon_social as nombre,
                STRING_AGG(pr.descripcion, ', ') as producto,
                SUM(dc.cantidad * dc.precio) as total,
                c.fecha_compra as fecha
            FROM cf_compras c
            INNER JOIN cf_empresa e ON c.id_empresa = e.id_empresa
            LEFT JOIN cf_detalle_compras dc ON c.id_compra = dc.id_compra
            LEFT JOIN cf_producto pr ON dc.id_producto = pr.id_producto
            WHERE c.estado_compra = 1
        ";
    
        // Aplicar filtros solo si se enviaron los valores
        if ($id_sede !== null) {
            $query .= " AND c.id_sede = :id_sede";
        }
        if ($id_usuario !== null) {
            $query .= " AND c.id_usuario = :id_usuario";
        }
    
        $query .= " GROUP BY c.id_compra, c.fecha_compra, e.razon_social";
    
        $consult = $this->pdo->prepare($query);
    
        // Asignar valores a los parámetros si existen
        if ($id_sede !== null) {
            $consult->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);
        }
        if ($id_usuario !== null) {
            $consult->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
    
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }
    
        
    
}
