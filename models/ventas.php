<?php
require_once '../config.php';
require_once 'conexion.php';

class Ventas {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    // Obtener todos los productos disponibles
    public function getProducts() {
        $consult = $this->pdo->prepare("SELECT * FROM cf_producto WHERE estado_producto = 1");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por su ID
    public function getProduct($id_producto) {
        $query = "SELECT * FROM cf_producto WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por su código de barras
    public function getBarcode($barcode) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_producto WHERE codigo_producto = ?");
        $consult->execute([$barcode]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    // Guardar una nueva venta
    public function saveVenta($id_cliente, $total, $metodo, $fecha, $id_user) {
        $consult = $this->pdo->prepare("INSERT INTO cf_ventas (id_cliente, total, metodo, fecha, id_usuario) VALUES (?, ?, ?, ?, ?)");
        $consult->execute([$id_cliente, $total, $metodo, $fecha, $id_user]);
        return $this->pdo->lastInsertId();
    }

    // Guardar los detalles de una venta
    public function saveDetalle($id_producto, $id_venta, $cantidad, $precio) {
        $consult = $this->pdo->prepare("INSERT INTO cf_detalle_ventas (id_producto, id_ventas, cantidad, precio) VALUES (?, ?, ?, ?)");
        return $consult->execute([$id_producto, $id_venta, $cantidad, $precio]);
    }

    // Actualizar el stock de un producto
    public function updateStock($stock, $id_producto) {
        $consult = $this->pdo->prepare("UPDATE cf_producto SET existencia = ? WHERE id_producto = ?");
        return $consult->execute([$stock, $id_producto]);
    }

    // Actualizar la deuda y capacidad del cliente
    public function updateDeudaCapacidad($id_cliente, $total) {
        $query = "EXEC actualizarDeudaCapacidad ?, ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id_cliente, $total]);
    }
}
