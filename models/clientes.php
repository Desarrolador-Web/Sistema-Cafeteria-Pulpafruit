<?php
require_once '../config.php';
require_once 'conexion.php';

class ClientesModel {
    private $pdo;

    public function __construct() {
        $this->pdo = new Conexion();
        $this->pdo = $this->pdo->conectar();
    }

    public function listarInformacionCajas() {
        $sql = "SELECT 
                    CONCAT(u.nombres, ' ', u.apellidos) AS nombre_completo,
                    ca.nombre_caja AS nombre_sede,
                    c.fecha_apertura,
                    c.fecha_cierre,
                    c.valor_cierre
                FROM cf_informacion_cajas c
                INNER JOIN cf_usuario u ON c.id_usuario = u.id_usuario
                INNER JOIN cf_caja ca ON c.id_sede = ca.id_caja";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
