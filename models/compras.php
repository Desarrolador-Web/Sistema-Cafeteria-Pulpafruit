<?php
require_once '../config.php';
require_once 'conexion.php';
class Productos{
    private $pdo, $con;
    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getProducts()
    {
        $consult = $this->pdo->prepare("SELECT * FROM cf_producto WHERE estado_producto = 1");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProduct($id)
    {
        $consult = $this->pdo->prepare("SELECT * FROM cf_producto WHERE id_producto = ?");
        $consult->execute([$id]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function comprobarBarcode($barcode)
    {
        $consult = $this->pdo->prepare("SELECT * FROM cf_producto WHERE codigo_producto = ?");
        $consult->execute([$barcode]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function saveProduct($barcode, $nombre, $precio_compra, $precio_venta, $stock, $imagen, $id_proveedor)
    {
        $consult = $this->pdo->prepare("INSERT INTO cf_producto (codigo_producto, descripcion, precio_compra, precio_venta, existencia, imagen, id_proveedor, estado_producto) VALUES (?,?,?,?,?,?,?,1)");
        return $consult->execute([$barcode, $nombre, $precio_compra, $precio_venta, $stock, $imagen, $id_proveedor]);
    }

    public function deleteProducto($id)
    {
        $consult = $this->pdo->prepare("UPDATE cf_producto SET estado_producto = 0 WHERE id_producto = ?");
        return $consult->execute([$id]);
    }

    public function getProductsBySede($id_sede) {
        // Solo muestra productos cuyo id_caja coincida con el id_sede y que estén activos (estado_producto = 1)
        $consult = $this->pdo->prepare("SELECT * FROM cf_producto WHERE estado_producto = 1 AND id_caja = ?");
        $consult->execute([$id_sede]);
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function updateProduct($barcode, $nombre, $precio_compra, $precio_venta, $stock, $imagen, $id_proveedor, $id)
    {
        $consult = $this->pdo->prepare("UPDATE cf_producto SET codigo_producto=?, descripcion=?, precio_compra=?, precio_venta=?, existencia=?, imagen=?, id_proveedor=? WHERE id_producto=?");
        return $consult->execute([$barcode, $nombre, $precio_compra, $precio_venta, $stock, $imagen, $id_proveedor, $id]);
    }
}


