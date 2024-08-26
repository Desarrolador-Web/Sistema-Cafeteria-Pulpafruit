<?php
require_once __DIR__ . '/../config.php';
require_once 'conexion.php';

class UsuariosModel {
     
    private $pdo, $con;
    
    public function __construct() {
        $this->con = new Conexion();
        $this->pdo = $this->con->conectar();
    }

    public function getLoginByCedula($cedula) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_usuario WHERE id_usuario = ?");
        $consult->execute([$cedula]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsers() {
        $consult = $this->pdo->prepare("SELECT u.*, c.nombre_caja FROM cf_usuario u INNER JOIN cf_caja c ON u.sede = c.id_caja WHERE u.estado_usuario = 1");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }  

    public function getUser($id) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_usuario WHERE id_usuario = ?");
        $consult->execute([$id]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function comprobarCedula($cedula) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_usuario WHERE id_usuario = ?");
        $consult->execute([$cedula]);
        return $consult->fetch(PDO::FETCH_ASSOC);
    }

    public function saveUser($cedula, $nombres, $apellidos, $correo, $clave, $ubicacion) {
        $consult = $this->pdo->prepare("INSERT INTO cf_usuario (id_usuario, nombres, apellidos, correo, clave, estado_usuario, id_autorizacion, sede) VALUES (?,?,?,?,?,1,1,?)");
        return $consult->execute([$cedula, $nombres, $apellidos, $correo, $clave, $ubicacion]);
    }

    public function deleteUser($id) {
        $consult = $this->pdo->prepare("UPDATE cf_usuario SET estado_usuario = ? WHERE id_usuario = ?");
        return $consult->execute([0, $id]);
    }

    public function updateUser($nombres, $apellidos, $correo, $id) {
        $consult = $this->pdo->prepare("UPDATE cf_usuario SET nombres=?, apellidos=?, correo=? WHERE id_usuario=?");
        return $consult->execute([$nombres, $apellidos, $correo, $id]);
    }

    public function getPermisos() {
        $consult = $this->pdo->prepare("SELECT * FROM cf_permisos");
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetalle($id_user) {
        $consult = $this->pdo->prepare("SELECT * FROM cf_detalle_permisos WHERE id_usuario = ?");
        $consult->execute([$id_user]);
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    public function savePermiso($permiso, $id_user) {
        // Validar que los valores sean enteros
        if (is_numeric($permiso) && is_numeric($id_user)) {
            $consult = $this->pdo->prepare("INSERT INTO cf_detalle_permisos (id_permiso, id_usuario) VALUES (?,?)");
            return $consult->execute([$permiso, $id_user]);
        } else {
            throw new Exception("Valores inválidos para permiso o id_user");
        }
    }
    
    public function eliminarPermisos($id_user) {
        $consult = $this->pdo->prepare("DELETE FROM cf_detalle_permisos WHERE id_usuario = ?");
        return $consult->execute([$id_user]);
    }
}
