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
    public function updateDeudaCapacidad($id_cliente, $total, $metodo) {
        // Se asegura de que los tres parámetros se pasen al procedimiento almacenado
        $query = "EXEC actualizarDeudaCapacidad @p_id_cliente = ?, @p_total = ?, @p_metodo = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id_cliente, $total, $metodo]);
    }
    
}
