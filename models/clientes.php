<?php
require_once '../config.php';
require_once 'conexion.php';

class ClientesModel {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
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

    public function getIniciosSesion() {
        $sql = "SELECT 
                    CONCAT(u.nombres, ' ', u.apellidos) AS nombre_completo,
                    ca.nombre_caja AS nombre_sede,
                    FORMAT(c.fecha_apertura, 'yyyy-MM-dd') AS fecha_apertura,
                    FORMAT(c.fecha_apertura, 'HH:mm:ss') AS hora_apertura,
                    CASE 
                        WHEN c.sesion = 2 THEN 'Sesión en curso'
                        ELSE FORMAT(c.fecha_cierre, 'yyyy-MM-dd')
                    END AS fecha_cierre,
                    CASE 
                        WHEN c.sesion = 2 THEN 'Sesión en curso'
                        ELSE FORMAT(c.fecha_cierre, 'HH:mm:ss')
                    END AS hora_cierre
                FROM cf_informacion_cajas c
                INNER JOIN cf_usuario u ON c.id_usuario = u.id_usuario
                INNER JOIN cf_caja ca ON c.id_sede = ca.id_caja";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUsuariosRegistrados() {
        $sql = "SELECT 
                    CONCAT(nombres, ' ', apellidos) AS nombre_completo,
                    CASE 
                        WHEN rol = 1 THEN 'S. Administrador'
                        WHEN rol = 2 THEN 'Administrador'
                        WHEN rol = 3 THEN 'Vendedor'
                    END AS rol
                FROM cf_usuario";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
