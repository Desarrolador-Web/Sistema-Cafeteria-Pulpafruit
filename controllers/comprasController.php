<?php
$option = isset($_GET['option']) ? $_GET['option'] : '';
require_once '../models/compras.php';

// Validar sesión
if (!isset($_SESSION['idusuario']) || !isset($_SESSION['rol'])) {
    echo json_encode(['tipo' => 'error', 'mensaje' => 'Sesión no válida. Inicie sesión nuevamente.']);
    exit;
}

// Instancia del modelo
$compras = new Compras();
$id_user = $_SESSION['idusuario'];
$rol_usuario = $_SESSION['rol'] ?? null;

// Validar si el usuario tiene rol 1 o 2
$mostrar_todos = in_array($rol_usuario, [1, 2]);

switch ($option) {
    case 'listarProductos':
        if (isset($_SESSION['id_sede'])) {
            $id_caja = $_SESSION['id_sede'];
            $result = $compras->getProductosPorSede($id_caja);
    
            if (isset($result['tipo']) && $result['tipo'] === 'error') {
                echo json_encode($result);
            } else {
                echo json_encode($result);
            }
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede.']);
        }
        break;
    
    
    
    case 'listarEmpresas':
        if (method_exists($compras, 'getEmpresas')) {
            $result = $compras->getEmpresas();
            echo json_encode($result);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'El método getEmpresas no está definido en el modelo Compras.']);
        }
        break;

    case 'registrarCompra':
        if (!isset($_SESSION['id_sede'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede. Por favor abra una caja.']);
            exit;
        }

        // Capturar datos enviados por POST
        $id_empresa = isset($_POST['id_empresa']) ? (int) $_POST['id_empresa'] : 0;
        $metodo_compra = isset($_POST['metodo_compra']) ? (int) $_POST['metodo_compra'] : 0;
        $id_caja = isset($_SESSION['id_sede']) ? (int) $_SESSION['id_sede'] : 0;
        $precio_compra = isset($_POST['precio_compra']) ? (float) $_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float) $_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        $estado = isset($_POST['estado']) ? (int) $_POST['estado'] : 1;
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
        $fecha = date('Y-m-d H:i:s');
        $imagen = '';

        if (empty($id_empresa) || empty($id_caja) || $metodo_compra === 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Datos incompletos para registrar la compra.']);
            exit;
        }

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
            move_uploaded_file($_FILES['imagen']['tmp_name'], '../' . $imagen);
        }

        $total = $precio_compra * $cantidad;

        $compraId = $compras->saveCompra($id_empresa, $total, $fecha, $id_user, $estado, $id_caja, $metodo_compra);

        if (!$compraId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al registrar la compra.']);
            exit;
        }

        $productId = $compras->saveProduct(
            $barcode,
            $descripcion,
            $id_empresa,
            $precio_compra,
            $precio_venta,
            $imagen,
            $cantidad,
            $estado,
            $id_caja
        );

        if (!$productId) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al guardar el producto.']);
            exit;
        }

        $result = $compras->saveDetalle($productId, $compraId, $cantidad, $precio_compra);

        if (!$result) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al guardar el detalle de la compra.']);
            exit;
        }

        echo json_encode(['tipo' => 'success', 'mensaje' => 'Compra registrada con éxito.']);
        break;

    case 'listarComprasPendientes':
        $mostrar_todos = in_array($_SESSION['rol'], [1, 2]);

        if ($mostrar_todos) {
            if (method_exists($compras, 'getAllComprasPendientes')) {
                $result = $compras->getAllComprasPendientes();
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'El método getAllComprasPendientes no está definido en el modelo Compras.']);
                exit;
            }
        } else {
            if (isset($_SESSION['id_sede']) && is_numeric($_SESSION['id_sede'])) {
                $id_caja = (int)$_SESSION['id_sede'];
                if (method_exists($compras, 'getComprasPendientesBySede')) {
                    $result = $compras->getComprasPendientesBySede($id_caja);
                } else {
                    echo json_encode(['tipo' => 'error', 'mensaje' => 'El método getComprasPendientesBySede no está definido en el modelo Compras.']);
                    exit;
                }
            } else {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'ID de sede inválido']);
                exit;
            }
        }

        echo json_encode($result);
        break;

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
