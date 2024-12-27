<?php
require_once '../config.php';
require_once 'conexion.php';

class Compras {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    
    // Obtener todas las compras pendientes (para roles 1 y 2).

    public function getAllComprasPendientes() {
        try {
            $consult = $this->pdo->prepare("
                SELECT 
                    c.id_compra AS idcompra,
                    c.total_compra,
                    c.fecha_compra,
                    c.estado_compra,
                    CONCAT(u.nombres, ' ', u.apellidos) AS usuario,
                    e.razon_social AS empresa,
                    c.metodo_compra,
                    c.id_caja
                FROM 
                    cf_compras c
                JOIN 
                    cf_usuario u ON c.id_usuario = u.id_usuario
                JOIN 
                    cf_empresa e ON c.id_empresa = e.id_empresa
                WHERE 
                    c.estado_compra = 0
                ORDER BY 
                    c.fecha_compra DESC
            ");
            $consult->execute();
            return $consult->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAllComprasPendientes: " . $e->getMessage());
            return [];
        }
    }

    public function getProductosPorSede($id_caja) {
        try {
            $query = "SELECT id_producto, codigo_producto, descripcion, existencia, 
                             precio_compra, precio_venta, imagen, id_empresa, estado_producto, id_caja 
                      FROM cf_producto 
                      WHERE id_caja = :id_caja";
    
            $stmt = $this->pdo->prepare($query); 
            $stmt->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos por sede: " . $e->getMessage());
            return ['tipo' => 'error', 'mensaje' => 'Error al obtener los productos por sede.'];
        }
    }


    
    // Obtener compras pendientes filtradas por sede (para otros roles).
    
    public function getComprasPendientes($id_caja, $rol) {
        try {
            if ($rol == 1 || $rol == 2) {
                $sql = "SELECT * FROM cf_compras WHERE estado_compra = 0 ORDER BY fecha_compra DESC";
                $query = $this->db->query($sql);
            } else {
                $sql = "SELECT * FROM cf_compras WHERE estado_compra = 0 AND id_caja = ? ORDER BY fecha_compra DESC";
                $query = $this->db->prepare($sql);
                $query->execute([$id_caja]);
            }
    
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    public function getProducts($id_caja) {
        try {
            // Depurar el parámetro
            error_log('ID Caja: ' . $id_caja);
    
            // Consulta SQL para obtener productos por caja
            $sql = "SELECT * FROM cf_productos WHERE id_caja = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_caja]);
    
            // Verificar si hay resultados
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (!$result) {
                error_log('No se encontraron productos para la caja: ' . $id_caja);
                return ['tipo' => 'error', 'mensaje' => 'No hay productos disponibles para esta caja.'];
            }
    
            return $result;
        } catch (PDOException $e) {
            error_log('Error SQL en getProducts: ' . $e->getMessage());
            return ['tipo' => 'error', 'mensaje' => 'Error al obtener los productos: ' . $e->getMessage()];
        }
    }
    
    

    
    // Guardar una compra en la base de datos.

    public function saveCompra($id_empresa, $total, $fecha, $id_user, $estado, $id_caja, $metodo_compra) {
        try {
            $sql = "
                INSERT INTO cf_compras (id_empresa, total_compra, fecha_compra, id_usuario, estado_compra, id_caja, metodo_compra) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_empresa, $total, $fecha, $id_user, $estado, $id_caja, $metodo_compra]);
            return $this->pdo->lastInsertId(); // Devuelve el id_compra generado
        } catch (PDOException $e) {
            error_log("Error en saveCompra: " . $e->getMessage());
            return false;
        }
    }

    
    //Guardar un producto en la base de datos.

    public function saveProduct($barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja) {
        try {
            $consult = $this->pdo->prepare("
                INSERT INTO cf_producto (codigo_producto, descripcion, id_empresa, precio_compra, precio_venta, imagen, existencia, estado_producto, id_caja) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $consult->execute([$barcode, $descripcion, $id_empresa, $precio_compra, $precio_venta, $imagen, $cantidad, $estado, $id_caja]);
            return $this->pdo->lastInsertId(); // Devuelve el id_producto generado
        } catch (PDOException $e) {
            error_log("Error en saveProduct: " . $e->getMessage());
            return false;
        }
    }

    
    // Guardar el detalle de una compra.

    public function saveDetalle($id_producto, $id_compra, $cantidad, $precio) {
        try {
            $consult = $this->pdo->prepare("
                INSERT INTO cf_detalle_compras (id_producto, id_compra, cantidad, precio) 
                VALUES (?, ?, ?, ?)
            ");
            return $consult->execute([$id_producto, $id_compra, $cantidad, $precio]);
        } catch (PDOException $e) {
            error_log("Error en saveDetalle: " . $e->getMessage());
            return false;
        }
    }


    // Listar todas las empresas disponibles.
    public function getEmpresas() {
        try {
            $consult = $this->pdo->prepare("SELECT id_empresa, razon_social FROM cf_empresa");
            $consult->execute();
            return $consult->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEmpresas: " . $e->getMessage());
            return [];
        }
    }

    // Obtener sede por usuario.

    public function getSedeUsuario($id_usuario) {
        try {
            $consult = $this->pdo->prepare("
                SELECT sede 
                FROM cf_usuario 
                WHERE id_usuario = ?
            ");
            $consult->execute([$id_usuario]);
            $result = $consult->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['sede'] : null;
        } catch (PDOException $e) {
            error_log("Error en getSedeUsuario: " . $e->getMessage());
            return null;
        }
    }

    // Actualizar estado y código de barras de un producto.

    public function updateEstadoProducto($id_producto, $estado, $barcode) {
        try {
            $this->pdo->beginTransaction();

            $this->pdo->prepare("
                UPDATE cf_producto 
                SET codigo_producto = ? 
                WHERE id_producto = ? AND (codigo_producto IS NULL OR codigo_producto = '')
            ")->execute([$barcode, $id_producto]);

            $this->pdo->prepare("
                UPDATE cf_producto 
                SET estado_producto = ? 
                WHERE id_producto = ?
            ")->execute([$estado, $id_producto]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error en updateEstadoProducto: " . $e->getMessage());
            return false;
        }
    }
}
