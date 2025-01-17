<?php
require_once '../config.php';
require_once 'conexion.php';

class Compras {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getProducts($id_caja, $rolUsuario) {
        if ($rolUsuario == '1' or $rolUsuario == '2') {
            $sql = "SELECT 
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
                    WHERE c.estado_compra = 0;";
        } else if ($rolUsuario == "3") {
            $sql = "SELECT 
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
                    WHERE c.estado_compra = 1;
                    ";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveCompra($id_empresa, $total, $fecha, $id_user, $estado, $id_caja, $metodo_compra) {
        $fecha_hora = date('Y-m-d H:i:s');
        $sql = "INSERT INTO cf_compras (id_empresa, total_compra, fecha_compra, id_usuario, estado_compra, id_caja, metodo_compra)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_empresa, $total, $fecha_hora, $id_user, $estado, $id_caja, $metodo_compra]);
        return $stmt->errorCode() == '00000' ? $this->pdo->lastInsertId() : false;
    }
    

    public function saveProduct($barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja) {
        $consult = $this->pdo->prepare("INSERT INTO cf_producto (codigo_producto, descripcion, id_empresa, precio_compra, precio_venta, imagen, existencia, estado_producto, id_caja) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $success = $consult->execute([$barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja]);
        return $success ? $this->pdo->lastInsertId() : false;
    }

    public function saveDetalle($id_producto, $id_compra, $cantidad, $precio) {
        $consult = $this->pdo->prepare("INSERT INTO cf_detalle_compras (id_producto, id_compra, cantidad, precio) VALUES (?,?,?,?)");
        return $consult->execute([$id_producto, $id_compra, $cantidad, $precio]);
    }

    public function getEmpresas() {
        $consult = $this->pdo->prepare("SELECT id_empresa, razon_social FROM cf_empresa");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSedeUsuario($id_usuario) {
        $consult = $this->pdo->prepare("SELECT sede FROM cf_usuario WHERE id_usuario = ?");
        $consult->execute([$id_usuario]);
        $result = $consult->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['sede'] : null;
    }

    public function guardarCompraDesdeModal($sede, $metodo) {
        try {
            $sql = "INSERT INTO cf_compras (id_caja, metodo_compra, fecha_compra, estado_compra)
                    VALUES (?, ?, GETDATE(), 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$sede, $metodo]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Error al guardar los datos del modal: " . $e->getMessage();
            return false;
        }
    }

    public function updateEstadoProducto($id_producto, $estado, $barcode) {
        try {
            // Primero, actualizaciòn de el código de barras si está vacío
            $consult = $this->pdo->prepare("UPDATE cf_producto SET codigo_producto = ? WHERE id_producto = ? AND (codigo_producto IS NULL OR codigo_producto = '')");
            $consult->execute([$barcode, $id_producto]);

            // Luego, actualizaciòn de el estado del producto
            $consult = $this->pdo->prepare("UPDATE cf_producto SET estado_producto = ? WHERE id_producto = ?");
            return $consult->execute([$estado, $id_producto]);
        } catch (PDOException $e) {
            echo "Error en la actualización: " . $e->getMessage();
            return false;
        }
    }

    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function commit() {
        $this->pdo->commit();
    }

    public function rollBack() {
        $this->pdo->rollBack();
    }
}