<?php
require_once '../config.php';
require_once 'conexion.php';

class Compras {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getProducts($id_caja, $rolUsuario): array {
        $sql = ($rolUsuario == '1' || $rolUsuario == '2') 
            ? "SELECT 
                    c.id_compra AS Id,
                    p.codigo_producto AS Barcode,
                    p.descripcion AS Descripción,
                    p.precio_compra AS Precio_Compra,
                    p.precio_venta AS Precio_Venta,
                    e.razon_social AS Proveedor,
                    p.imagen AS Imagen,
                    dc.cantidad AS Cantidad
                FROM cf_compras c
                INNER JOIN cf_detalle_compras dc ON c.id_compra = dc.id_compra
                INNER JOIN cf_producto p ON dc.id_producto = p.id_producto
                INNER JOIN cf_empresa e ON c.id_empresa = e.id_empresa
                WHERE c.estado_compra = 0"
            : "SELECT 
                    c.id_compra AS Id,
                    p.codigo_producto AS Barcode,
                    p.descripcion AS Descripción,
                    p.precio_compra AS Precio_Compra,
                    p.precio_venta AS Precio_Venta,
                    e.razon_social AS Proveedor,
                    p.imagen AS Imagen,
                    dc.cantidad AS Cantidad
                FROM cf_compras c
                INNER JOIN cf_detalle_compras dc ON c.id_compra = dc.id_compra
                INNER JOIN cf_producto p ON dc.id_producto = p.id_producto
                INNER JOIN cf_empresa e ON c.id_empresa = e.id_empresa
                WHERE c.estado_compra = 0 AND dc.id_caja = :id_caja";
    
        $stmt = $this->pdo->prepare($sql);
    
        if ($rolUsuario == '3') {
            $stmt->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProductoPorBarcode($barcode) {
        $sql = "SELECT id_producto, precio_compra, precio_venta, existencia FROM cf_producto WHERE codigo_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$barcode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveCompra($sede, $total, $id_usuario, $estado, $id_caja, $metodo_compra) {
        date_default_timezone_set('America/Bogota');
        $fecha = date('Y-m-d H:i:s');
    
        $sql = "INSERT INTO cf_compras (id_empresa, total_compra, fecha_compra, id_usuario, estado_compra, id_caja, metodo_compra)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sede, $total, $fecha, $id_usuario, $estado, $id_caja, $metodo_compra]);
    
        return $stmt->errorCode() == '00000' ? $this->pdo->lastInsertId() : false;
    }
    

    public function saveOrUpdateProduct($barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja) {
        $sql_check = "SELECT id_producto, existencia FROM cf_producto WHERE codigo_producto = ? AND estado_producto = 1 AND id_caja = ?";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute([$barcode, $id_caja]);
        $producto_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
        if ($producto_existente) {
            $nueva_existencia = $producto_existente['existencia'] + $cantidad;
            $sql_update = "UPDATE cf_producto SET existencia = ?, precio_compra = ?, precio_venta = ?, imagen = ? WHERE id_producto = ?";
            $stmt_update = $this->pdo->prepare($sql_update);
            $stmt_update->execute([$nueva_existencia, $precio_compra, $precio_venta, $imagen, $producto_existente['id_producto']]);
            return $producto_existente['id_producto'];
        }
    }

    public function saveDetalle($id_producto, $id_compra, $cantidad, $precio) {
        $sql = "INSERT INTO cf_detalle_compras (id_producto, id_compra, cantidad, precio) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_producto, $id_compra, $cantidad, $precio]);
    }
}
