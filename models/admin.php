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

    public function cerrarCajaConObservacion($id_usuario, $valorCierre, $fechaCierre, $observacion) {
        // Seleccionar el último registro del usuario donde la sesión sea 2
        $sql = "UPDATE cf_informacion_cajas 
                SET valor_cierre = ?, fecha_cierre = ?, observacion = ? 
                WHERE id_info_caja = (
                    SELECT TOP 1 id_info_caja 
                    FROM cf_informacion_cajas 
                    WHERE id_usuario = ? AND sesion = 2 
                    ORDER BY fecha_apertura DESC
                )";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$valorCierre, $fechaCierre, $observacion, $id_usuario]);
    }
    
    public function getEstadoCaja($id_user) {
        $sql = "SELECT 
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 
                            FROM cf_informacion_cajas 
                            WHERE id_usuario = ? AND sesion = 2
                        ) THEN 1 -- Existe sesión con valor 2
                        ELSE 2 -- No existe sesión con valor 2
                    END AS estado";
        
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_user]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            return (int)$result['estado']; // 1 = Tiene sesión 2, 2 = No tiene sesión 2
        }
        return 0; // Sin registros para el usuario
    }
    
    
    public function obtenerDiferenciaVentasCompras($id_usuario) {
        $sql = "
            DECLARE @usuarioCaja INT, 
                    @fechaApertura DATETIME, 
                    @valorApertura DECIMAL(10,2), 
                    @sedeCaja INT,
                    @totalVentas DECIMAL(10,2),
                    @totalCompras DECIMAL(10,2),
                    @totalFinal DECIMAL(10,2),
                    @totalArqueo DECIMAL(10,2);
    
            -- Capturar valores de la apertura de caja
            SELECT 
                @usuarioCaja = id_usuario,
                @fechaApertura = fecha_apertura, 
                @valorApertura = valor_apertura,
                @sedeCaja = id_sede
            FROM cf_informacion_cajas 
            WHERE id_usuario = ? 
            AND sesion = '2';
    
            -- Calcular la suma de ventas desde la fecha de apertura hasta el momento actual
            SELECT @totalVentas = COALESCE(SUM(total), 0)  
            FROM cf_ventas 
            WHERE id_usuario = @usuarioCaja
            AND metodo <> 3  
            AND fecha >= @fechaApertura  
            AND fecha <= GETDATE();  
    
            -- Calcular la suma de compras desde la fecha de apertura hasta el momento actual
            SELECT @totalCompras = COALESCE(SUM(total_compra), 0)  
            FROM cf_compras 
            WHERE id_usuario = @usuarioCaja
            AND fecha_compra >= @fechaApertura  
            AND fecha_compra <= GETDATE();  
    
            -- Calcular el total final (ventas + valor de apertura)
            SET @totalFinal = @totalVentas + @valorApertura;
    
            -- Calcular el Total_Arqueo (Total_Final - Total_Compras)
            SET @totalArqueo = @totalFinal - @totalCompras;
    
            -- Devolver solo el total de arqueo
            SELECT @totalArqueo AS Total_Arqueo;
        ";
    
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario]);
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
