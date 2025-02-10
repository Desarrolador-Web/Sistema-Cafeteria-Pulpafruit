<?php
require_once '../config.php';
require_once 'conexion.php';

class Compras {
    private $pdo;

    public function __construct() {
        $this->pdo = (new Conexion())->conectar();
    }

    // Obtener productos desde la base de datos
    public function getProducts($id_caja, $rolUsuario): array {
        // Consulta SQL dependiendo del rol del usuario
        $sql = ($rolUsuario == 1 || $rolUsuario == 2) 
            ? "SELECT 
                    p.id_producto AS Id,
                    p.codigo_producto AS Barcode,
                    p.descripcion AS Descripción,
                    p.precio_compra AS Precio_Compra,
                    p.precio_venta AS Precio_Venta,
                    e.razon_social AS Proveedor,
                    p.imagen AS Imagen,
                    p.existencia AS Cantidad
                FROM cf_producto p
                INNER JOIN cf_empresa e ON p.id_empresa = e.id_empresa
                WHERE p.estado_producto = 1"
            : "SELECT 
                    p.id_producto AS Id,
                    p.codigo_producto AS Barcode,
                    p.descripcion AS Descripción,
                    p.precio_compra AS Precio_Compra,
                    p.precio_venta AS Precio_Venta,
                    e.razon_social AS Proveedor,
                    p.imagen AS Imagen,
                    p.existencia AS Cantidad
                FROM cf_producto p
                INNER JOIN cf_empresa e ON p.id_empresa = e.id_empresa
                WHERE p.estado_producto = 1 AND p.id_caja = :id_caja";

        $stmt = $this->pdo->prepare($sql);

        // Enlazar el parámetro id_caja si el rol del usuario es 3
        if ($rolUsuario == 3) {
            $stmt->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guardar o actualizar un producto
    public function saveProduct($barcode, $descripcion, $precio_compra, $precio_venta, $cantidad, $estado_producto, $id_empresa, $id_caja, $imagen) {
        // Verificar si el producto ya existe con el mismo código de barras y la misma sede
        $sql_check = "SELECT id_producto, existencia FROM cf_producto WHERE codigo_producto = ? AND estado_producto = 1 AND id_caja = ?";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute([$barcode, $id_caja]);
        $producto_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($producto_existente) {
            // Si el producto ya existe, actualizar su cantidad y precios
            $nueva_existencia = $producto_existente['existencia'] + $cantidad;
            $sql_update = "UPDATE cf_producto 
                           SET existencia = ?, precio_compra = ?, precio_venta = ?, imagen = ? 
                           WHERE id_producto = ?";
            $stmt_update = $this->pdo->prepare($sql_update);
            $stmt_update->execute([$nueva_existencia, $precio_compra, $precio_venta, $imagen, $producto_existente['id_producto']]);
            return $producto_existente['id_producto']; // Retornar el ID del producto actualizado
        } else {
            // Si el producto no existe, insertar un nuevo registro
            $sql_insert = "INSERT INTO cf_producto 
                           (codigo_producto, descripcion, precio_compra, precio_venta, existencia, estado_producto, id_empresa, id_caja, imagen) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $this->pdo->prepare($sql_insert);
            $stmt_insert->execute([$barcode, $descripcion, $precio_compra, $precio_venta, $cantidad, $estado_producto, $id_empresa, $id_caja, $imagen]);
            return $stmt_insert->errorCode() == '00000' ? $this->pdo->lastInsertId() : false;
        }
    }

    public function getProveedores(): array {
        $sql = "SELECT id_empresa, razon_social FROM cf_empresa";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
}
