<?php
require_once '../models/productos.php';
$productos = new Compras();
$option = isset($_GET['option']) ? $_GET['option'] : '';

switch ($option) {
    case 'listarProductos':
        // Validar que los parámetros necesarios estén presentes
        if (isset($_GET['id_caja']) && isset($_GET['rolUsuario'])) {
            $id_caja = intval($_GET['id_caja']);
            $rolUsuario = intval($_GET['rolUsuario']);

            // Obtener productos desde el modelo
            $productosLista = $productos->getProducts($id_caja, $rolUsuario);

            // Validar si se obtuvieron productos
            if (!empty($productosLista)) {
                echo json_encode(['productos' => $productosLista]);
            } else {
                echo json_encode(['error' => 'No se encontraron productos para los parámetros especificados.']);
            }
        } else {
            echo json_encode(['error' => 'Faltan parámetros necesarios: id_caja o rolUsuario']);
        }
        break;

    case 'registrarProducto':
        // Validar y capturar datos del formulario
        $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $precio_compra = isset($_POST['precio_compra']) ? (float)$_POST['precio_compra'] : 0.0;
        $precio_venta = isset($_POST['precio_venta']) ? (float)$_POST['precio_venta'] : 0.0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
        $id_empresa = isset($_POST['id_empresa']) ? (int)$_POST['id_empresa'] : 0;
        $id_caja = isset($_POST['id_caja']) ? (int)$_POST['id_caja'] : 0;
        $imagen = '';
        $estado_producto = 1; // Estado por defecto (1)

        // Subir imagen si está presente
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
            move_uploaded_file($_FILES['imagen']['tmp_name'], '../' . $imagen);
        }
    
        // Validar campos obligatorios
        if (empty($barcode) || empty($descripcion) || $precio_compra <= 0 || $precio_venta <= 0 || $cantidad <= 0 || $id_empresa <= 0 || $id_caja <= 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Todos los campos son obligatorios.']);
            break;
        }
    
        // Guardar el producto en la base de datos
        $result = $productos->saveProduct($barcode, $descripcion, $precio_compra, $precio_venta, $cantidad, $estado_producto, $id_empresa, $id_caja, $imagen);
    
        // Responder al frontend según el resultado
        if ($result) {
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Producto registrado con éxito.']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al registrar el producto.']);
        }
        break;
        
    case 'listarProveedores':
        require_once '../models/productos.php';
        $productos = new Compras();
    
        $proveedores = $productos->getProveedores();
        if (!empty($proveedores)) {
            echo json_encode(['proveedores' => $proveedores]);
        } else {
            echo json_encode(['error' => 'No se encontraron proveedores.']);
        }
        break;
    

    default:
        // Opción no válida
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida.']);
        break;
}
