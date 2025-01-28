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
    }
    
