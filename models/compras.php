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
        $sql = ($rolUsuario == '1' || $rolUsuario == '2') ?
            "SELECT 
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
            WHERE c.estado_compra = 0;"
            :
            "SELECT 
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
            WHERE c.estado_compra = 1;";

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
        $sql = "INSERT INTO cf_producto (codigo_producto, descripcion, id_empresa, precio_compra, precio_venta, imagen, existencia, estado_producto, id_caja)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja]);
        return $stmt->errorCode() == '00000' ? $this->pdo->lastInsertId() : false;
    }

    public function saveDetalle($id_producto, $id_compra, $cantidad, $precio) {
        $sql = "INSERT INTO cf_detalle_compras (id_producto, id_compra, cantidad, precio) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_producto, $id_compra, $cantidad, $precio]);
    }

    public function getEmpresas() {
        $sql = "SELECT id_empresa, razon_social FROM cf_empresa";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSedeUsuario($id_usuario) {
        $sql = "SELECT sede FROM cf_usuario WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['sede'] : null;
    }

    public function guardarCompraDesdeModal($sede, $metodo) {
        $sql = "INSERT INTO cf_compras (id_caja, metodo_compra, fecha_compra, estado_compra) VALUES (?, ?, GETDATE(), 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sede, $metodo]);
        return $stmt->rowCount() > 0;
    }

    public function updateEstadoProducto($id_producto, $estado, $barcode) {
        $sql = "
            UPDATE cf_producto
            SET 
                codigo_producto = COALESCE(NULLIF(?, ''), codigo_producto),
                estado_producto = ?
            WHERE id_producto = ?;

            UPDATE cf_compras
            SET estado_compra = ?
            WHERE id_compra IN (
                SELECT id_compra
                FROM cf_detalle_compras
                WHERE id_producto = ?
            );
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$barcode, $estado, $id_producto, $estado, $id_producto]);
        return $stmt->rowCount() > 0;
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