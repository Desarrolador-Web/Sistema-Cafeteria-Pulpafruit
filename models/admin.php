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
        $sql = "SELECT * FROM cf_informacion_cajas WHERE id_usuario = ? AND CONVERT(DATE, fecha_apertura) = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute([$id_usuario, $fechaHoy]);
        return $query->fetch(PDO::FETCH_ASSOC);  // Retorna un array si hay resultados, false si no
    }
    

    // Abrir caja, incluyendo el id_usuario
    public function abrirCaja($id_usuario, $valorApertura, $id_sede, $fechaApertura) {
        $sql = "INSERT INTO cf_informacion_cajas (id_usuario, valor_apertura, id_sede, fecha_apertura) VALUES (?, ?, ?, ?)";
        $query = $this->pdo->prepare($sql);
    
        // Imprimir los valores antes de ejecutar la consulta para ver si llegan correctamente
        var_dump($id_usuario, $valorApertura, $id_sede, $fechaApertura);
    
        // Intentar ejecutar la consulta y mostrar cualquier error de SQL
        if (!$query->execute([$id_usuario, $valorApertura, $id_sede, $fechaApertura])) {
            var_dump($query->errorInfo());  // Mostrar error de la base de datos
            return false;
        }
        
        return true;
    }
    
    
    
    
}
