<?php
require_once __DIR__ . '/../config.php';  // Usar __DIR__ para obtener la ruta absoluta al archivo de configuración
require_once 'conexion.php';   // Incluir la conexión a la base de datos

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
}
