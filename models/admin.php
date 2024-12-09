<?php
require_once __DIR__ . '/../config.php'; 
require_once 'conexion.php';   

class AdminModel {
    private $pdo;

    public function __construct() {
        $this->pdo = new Conexion();
        $this->pdo = $this->pdo->conectar();
    }

    // Verificar si la caja está abierta para el usuario en la fecha actual
    public function checkCajaAbierta($id_usuario, $fechaHoy) {
        $sql = "
            SELECT ic.* 
            FROM cf_informacion_cajas ic
            INNER JOIN cf_usuario u ON ic.id_usuario = u.id_usuario
            WHERE ic.id_usuario = ? 
              AND (CONVERT(DATE, ic.fecha_apertura) = ? AND ic.valor_cierre IS NULL
                   OR u.rol IN (1, 2))
        ";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario, $fechaHoy]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
     
    
    // Método que verifica si hay una caja sin cerrar
    public function checkCajaSinCerrar($id_sede) {
        $sql = "SELECT * FROM cf_informacion_cajas WHERE id_sede = ? AND valor_cierre IS NULL AND fecha_cierre IS NULL";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_sede]);
        return $query->fetch(PDO::FETCH_ASSOC);  // Retorna true si hay una caja sin cerrar
    }

    // Abrir caja, incluyendo el id_usuario
    public function abrirCaja($id_usuario, $valorApertura, $id_sede, $fechaApertura) {
        $sql = "INSERT INTO cf_informacion_cajas (id_usuario, valor_apertura, id_sede, fecha_apertura) VALUES (?, ?, ?, ?)";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$id_usuario, $valorApertura, $id_sede, $fechaApertura]);
    }

    public function cerrarCajaConObservacion($id_info_caja, $valorCierre, $fechaCierre, $observacion) {
        $sql = "UPDATE cf_informacion_cajas 
                SET valor_cierre = ?, fecha_cierre = ?, observacion = ? 
                WHERE id_info_caja = ?";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$valorCierre, $fechaCierre, $observacion, $id_info_caja]);
    }
    

    public function obtenerDiferenciaVentasCompras($id_usuario) {
        $sql = "

        WITH VentasTotales AS (
            SELECT SUM(v.total) AS TotalVentas
            FROM cf_ventas v
            INNER JOIN cf_informacion_cajas ic
                ON v.id_usuario = ic.id_usuario
            WHERE ic.id_usuario = ?
              AND ic.fecha_cierre IS NULL
              AND ic.valor_cierre IS NULL
              AND v.fecha >= ic.fecha_apertura  
              AND v.fecha <= GETDATE()  
        ),
        ComprasTotales AS (
            SELECT SUM(c.total_compra) AS TotalCompras
            FROM cf_compras c
            INNER JOIN cf_informacion_cajas ic
                ON c.id_usuario = ic.id_usuario
            WHERE ic.id_usuario = ?
              AND ic.fecha_cierre IS NULL
              AND ic.valor_cierre IS NULL
              AND c.fecha_compra >= ic.fecha_apertura  
              AND c.fecha_compra <= GETDATE()  
        )
        SELECT ISNULL(VentasTotales.TotalVentas, 0) - ISNULL(ComprasTotales.TotalCompras, 0) AS ResultadoFinal
        FROM VentasTotales, ComprasTotales;
        
        ";
        
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario, $id_usuario]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerCajaAbiertaUsuario($id_usuario) {
        // Cambia LIMIT por TOP 1, que es compatible con SQL Server
        $sql = "SELECT TOP 1 * FROM cf_informacion_cajas WHERE id_usuario = ? AND valor_cierre IS NULL AND fecha_cierre IS NULL ORDER BY fecha_apertura DESC";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function guardarObservacionYCodigo($id_info_caja, $observacion = null, $codigo = null) {
        $sql = "UPDATE cf_informacion_cajas SET ";
        $params = [];
        
        if (!is_null($observacion)) {
            $sql .= "observacion = ?";
            $params[] = $observacion;
        }
        
        if (!is_null($codigo)) {
            $sql .= (count($params) > 0 ? ", " : "") . "codigo = ?";
            $params[] = $codigo;
        }
        
        $sql .= " WHERE id_info_caja = ?";
        $params[] = (int)$id_info_caja; 
        
        $query = $this->pdo->prepare($sql);
        return $query->execute($params);
    }
    
    public function validarCodigo($id_info_caja, $codigoIngresado) {
        $sql = "SELECT codigo FROM cf_informacion_cajas WHERE id_info_caja = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_info_caja]);
        $codigoGuardado = $query->fetchColumn(); 
        
        // comparar ambos como cadenas
        return strval($codigoGuardado) === strval($codigoIngresado);
    }
    
    public function cerrarCaja($id_info_caja, $valorCierre, $fechaCierre) {
        $sql = "UPDATE cf_informacion_cajas SET valor_cierre = ?, fecha_cierre = ? WHERE id_info_caja = ?";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$valorCierre, $fechaCierre, $id_info_caja]);
    }
}
