<?php
require_once '../config.php';
require_once 'conexion.php';

class ClientesModel {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }
    
    // Obtener todos los clientes
    public function getClients() {
        $consult = $this->pdo->prepare("SELECT * FROM cf_cliente");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un cliente por su ID
    public function getClienteById($id_cliente) {
        $query = "SELECT * FROM cf_cliente WHERE id_cliente = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_cliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar la deuda y capacidad del cliente
    public function updateDeudaCapacidad($id_cliente, $total) {
        $query = "EXEC actualizarDeudaCapacidad ?, ?"; // Solo dos parámetros
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id_cliente, $total]);
    }

    // Muestra la información de las cajas
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

    // Consulta para los productos agotados
    public function getProductosAgotados() {
        $sql = "SELECT 
                    p.descripcion AS producto,
                    CONVERT(VARCHAR, MAX(c.fecha_compra), 23) AS ultima_fecha_compra, -- Solo fecha YYYY-MM-DD
                    CONVERT(VARCHAR, MAX(v.fecha), 23) AS ultima_fecha_venta         -- Solo fecha YYYY-MM-DD
                FROM cf_producto p
                LEFT JOIN cf_detalle_compras dc ON p.id_producto = dc.id_producto
                LEFT JOIN cf_compras c ON dc.id_compra = c.id_compra
                LEFT JOIN cf_detalle_ventas dv ON p.id_producto = dv.id_producto
                LEFT JOIN cf_ventas v ON dv.id_ventas = v.id_ventas
                WHERE p.existencia = 0
                GROUP BY p.descripcion";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    // Para la funcionalidad de productos agotados
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
