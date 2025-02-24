<?php
require_once '../config.php';
require_once 'conexion.php';

class ClientesModel {
    private $pdo, $con;

    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }
        
        // Función para obtener los inicios de sesión
        public function getIniciosSesion() {
            $query = "SELECT 
                        cf_usuario.nombres + ' ' + cf_usuario.apellidos AS nombre_completo,
                        cf_caja.nombre_caja AS nombre_sede,
                        cf_informacion_cajas.fecha_apertura,
                        FORMAT(cf_informacion_cajas.fecha_apertura, 'HH:mm:ss') AS hora_apertura,
                        cf_informacion_cajas.fecha_cierre,
                        FORMAT(cf_informacion_cajas.fecha_cierre, 'HH:mm:ss') AS hora_cierre
                      FROM cf_informacion_cajas
                      INNER JOIN cf_usuario ON cf_informacion_cajas.id_usuario = cf_usuario.id_usuario
                      INNER JOIN cf_caja ON cf_informacion_cajas.id_sede = cf_caja.id_caja";
            return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        }
    
        // Función para obtener los usuarios registrados
        public function getUsuariosRegistrados() {
            $query = "SELECT 
                        cf_usuario.nombres + ' ' + cf_usuario.apellidos AS nombre_completo,
                        cf_usuario.rol
                      FROM cf_usuario";
            return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        }
    
        // Función para obtener los productos agotados
        public function getProductosAgotados() {
            $query = "SELECT 
                        cf_producto.descripcion AS producto,
                        MAX(cf_compras.fecha_compra) AS ultima_fecha_compra,
                        MAX(cf_ventas.fecha) AS ultima_fecha_venta
                      FROM cf_producto
                      LEFT JOIN cf_detalle_compras ON cf_producto.id_producto = cf_detalle_compras.id_producto
                      LEFT JOIN cf_compras ON cf_detalle_compras.id_compra = cf_compras.id_compra
                      LEFT JOIN cf_detalle_ventas ON cf_producto.id_producto = cf_detalle_ventas.id_producto
                      LEFT JOIN cf_ventas ON cf_detalle_ventas.id_ventas = cf_ventas.id_ventas
                      WHERE cf_producto.existencia = 0
                      GROUP BY cf_producto.descripcion";
            return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getNivelarInventario() {
            $query = "SELECT 
                        codigo_producto,
                        descripcion,
                        existencia,
                        precio_compra,
                        precio_venta,
                        imagen 
                      FROM cf_producto";
            return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function actualizarProducto($codigo_producto, $existencia, $precio_compra, $precio_venta, $confirmacion) {
            // Obtener los valores actuales antes de actualizar
            $query = "SELECT existencia, precio_compra, precio_venta FROM cf_producto WHERE codigo_producto = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$codigo_producto]);
            $producto_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$producto_actual) {
                return ['tipo' => 'error', 'mensaje' => 'Producto no encontrado'];
            }
        
            $existencia_actual = $producto_actual['existencia'];
            $precio_compra_actual = $producto_actual['precio_compra'];
            $precio_venta_actual = $producto_actual['precio_venta'];
        
            // Verificar si hay reducción de valores y si el usuario no ha confirmado aún
            if (($existencia < $existencia_actual || $precio_compra < $precio_compra_actual || $precio_venta < $precio_venta_actual) && !$confirmacion) {
                return ['tipo' => 'alerta', 'mensaje' => 'Se detectó una reducción en los valores. ¿Está seguro de continuar?'];
            }
        
            // Si el usuario confirmó o no hay reducción, actualizar el producto
            $query = "UPDATE cf_producto SET existencia = ?, precio_compra = ?, precio_venta = ? WHERE codigo_producto = ?";
            $stmt = $this->pdo->prepare($query);
            if ($stmt->execute([$existencia, $precio_compra, $precio_venta, $codigo_producto])) {
                return ['tipo' => 'success', 'mensaje' => 'Producto actualizado correctamente'];
            } else {
                return ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el producto'];
            }
        }
                
}