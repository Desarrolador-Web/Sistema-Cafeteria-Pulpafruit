<?php
require_once '../models/usuarios.php';


$option = (empty($_GET['option'])) ? '' : $_GET['option'];
$usuarios = new UsuariosModel();
switch ($option) {
    case 'acceso':
        $accion = file_get_contents('php://input');
        $array = json_decode($accion, true);
        $cedula = $array['cedula'];
        $password = $array['password'];
        $result = $usuarios->getLoginByCedula($cedula);
        if (empty($result)) {
            $res = array('tipo' => 'error', 'mensaje' => 'CÉDULA NO EXISTE');
        } else {
            if (password_verify($password, $result['clave'])) {
                $_SESSION['nombre'] = $result['nombres'] . ' ' . $result['apellidos'];
                $_SESSION['correo'] = $result['correo'];
                $_SESSION['idusuario'] = $result['id_usuario'];
                $res = array('tipo' => 'success', 'mensaje' => 'ok');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'CONTRASEÑA INCORRECTA');
            }
        }

        echo json_encode($res);
        break;

    case 'listar':
        $data = $usuarios->getUsers();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['nombre_completo'] = $data[$i]['nombres'] . ' ' . $data[$i]['apellidos'];
            $data[$i]['sede_nombre'] = $data[$i]['nombre_caja']; // Añadir el nombre de la sede
            $data[$i]['accion'] = '<div class="d-flex">
                <a class="btn btn-danger btn-sm" onclick="deleteUser(' . $data[$i]['id_usuario'] . ')"><i class="fas fa-eraser"></i></a>
                <a class="btn btn-primary btn-sm" onclick="editUser(' . $data[$i]['id_usuario'] . ')"><i class="fas fa-edit"></i></a>
                <a class="btn btn-info btn-sm" onclick="permisos(' . $data[$i]['id_usuario'] . ')"><i class="fas fa-lock"></i></a>
                </div>';
        }
        echo json_encode($data);
        break;

    case 'save':
        $cedula = $_POST['cedula'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $correo = $_POST['correo'];
        $clave = $_POST['clave'];
        $ubicacion = $_POST['ubicacion']; // Capturar el valor del select 'ubicacion'
        $id_user = $_POST['id_user'];
        if ($id_user == '') {
            $consult = $usuarios->comprobarCedula($cedula);
            if (empty($consult)) {
                $hash = password_hash($clave, PASSWORD_DEFAULT);
                $result = $usuarios->saveUser($cedula, $nombres, $apellidos, $correo, $hash, $ubicacion);
                if ($result) {
                    $res = array('tipo' => 'success', 'mensaje' => 'USUARIO REGISTRADO');
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL AGREGAR');
                }
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'LA CÉDULA YA EXISTE');
            }
        } else {
            $result = $usuarios->updateUser($nombres, $apellidos, $correo, $ubicacion, $id_user);
            if ($result) {
                $res = array('tipo' => 'success', 'mensaje' => 'USUARIO MODIFICADO');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL MODIFICAR');
            }
        }
        echo json_encode($res);
        break;
    case 'delete':
        $id = $_GET['id'];
        $data = $usuarios->deleteUser($id);
        if ($data) {
            $res = array('tipo' => 'success', 'mensaje' => 'USUARIO ELIMINADO');
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL ELIMINAR');
        }
        echo json_encode($res);
        break;
    case 'edit':
        $id = $_GET['id'];
        $data = $usuarios->getUser($id);
        echo json_encode($data);
        break;
    case 'permisos':
        $id = $_GET['id'];
        $data['permisos'] = $usuarios->getPermisos();
        $consulta = $usuarios->getDetalle($id);
        $datos = array();
        foreach ($consulta as $asignado) {
            $datos[$asignado['id_permiso']] = true;
        }
        $data['asig'] = $datos;
        echo json_encode($data);
        break;
    case 'savePermiso':
        $id_user = $_POST['id_usuario'];
        $usuarios->eliminarPermisos($id_user);
        $res = true;
        if (!empty($_POST['permisos'])) {
            foreach ($_POST['permisos'] as $permiso) {
                if ($permiso !== 'undefined') {
                    $res = $usuarios->savePermiso($permiso, $id_user);
                }
            }
            if ($res) {
                $res = array('tipo' => 'success', 'mensaje' => 'PERMISOS ASIGNADO');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL AGREGAR LOS PERMISOS');
            }
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'NO SE HAN ENVIADO PERMISOS VÁLIDOS');
        }
        echo json_encode($res);
        break;

    case 'logout':
        // Destruir la sesión

        $usuarios->logout();
        break;
    default:
        # code...
        break;
}
