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

    // Guardar un producto (nuevo registro)
    public function saveProduct($barcode, $descripcion, $precio_compra, $precio_venta, $cantidad, $id_empresa, $id_caja, $imagen) {
        $sql = $this->pdo->prepare("INSERT INTO cf_producto 
                                    (codigo_producto, descripcion, precio_compra, precio_venta, existencia, estado_producto, id_empresa, id_caja, imagen) 
                                    VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?)");
        return $sql->execute([$barcode, $descripcion, $precio_compra, $precio_venta, $cantidad, $id_empresa, $id_caja, $imagen]);
    }    

    // Verificar si un producto ya existe (por barcode)
    public function verificarProductoPorBarcode($barcode): bool {
        $sql = "SELECT COUNT(*) AS total FROM cf_producto WHERE codigo_producto = ? AND estado_producto = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$barcode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    // Obtener la lista de proveedores
    public function getProveedores(): array {
        $sql = "SELECT id_empresa, razon_social FROM cf_empresa";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
