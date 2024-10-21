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
        $sql = "SELECT * FROM cf_informacion_cajas WHERE id_usuario = ? AND CONVERT(DATE, fecha_apertura) = ? AND valor_cierre IS NULL";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario, $fechaHoy]);
        return $query->fetch(PDO::FETCH_ASSOC);  // Retorna true si hay una caja abierta y no cerrada
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

    public function obtenerCajaAbiertaUsuario($id_usuario) {
        // Cambia LIMIT por TOP 1, que es compatible con SQL Server
        $sql = "SELECT TOP 1 * FROM cf_informacion_cajas WHERE id_usuario = ? AND valor_cierre IS NULL AND fecha_cierre IS NULL ORDER BY fecha_apertura DESC";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    public function cerrarCaja($id_info_caja, $valorCierre, $fechaCierre) {
        $sql = "UPDATE cf_informacion_cajas SET valor_cierre = ?, fecha_cierre = ? WHERE id_info_caja = ?";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$valorCierre, $fechaCierre, $id_info_caja]);
    }
    
}
