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
    
    

