<?php
require_once '../models/ventas.php';
require_once '../models/clientes.php';

date_default_timezone_set('America/Bogota'); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ventas = new Ventas();
$clientes = new ClientesModel();
$id_user = $_SESSION['idusuario'];

$option = isset($_GET['option']) ? $_GET['option'] : '';

switch ($option) {
    
    case 'listar':
        if (isset($_SESSION['id_sede'])) {
            $id_sede = $_SESSION['id_sede'];
            $result = $ventas->getProductsBySede($id_sede);
            
            foreach ($result as $i => $item) {
                // Obtener cantidad de compra inicial
                $cantidadCompraInicial = $ventas->getCantidadCompraInicial($item['id_producto']);
                
                if ($cantidadCompraInicial !== null) {
                    // Calcular porcentaje del stock actual en base a la cantidad de compra inicial
                    $porcentajeStock = ($item['existencia'] / $cantidadCompraInicial) * 100;
                    $result[$i]['porcentajeStock'] = $porcentajeStock; // se añade el porcentaje
                } else {
                    $result[$i]['porcentajeStock'] = 100; // stock completo si no se encuentra el valor inicial
                }
    
                // Añadir botón de carrito sin modificar `cantidad`
                $result[$i]['addcart'] = '<a href="#" class="btn btn-primary btn-sm" onclick="addCart(' . $item['id_producto'] . ')"><i class="fas fa-cart-plus"></i></a>';
            }
            echo json_encode($result);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se ha seleccionado ninguna sede.']);
        }
        break;
    
    case 'listarClientes':
        try {
            $datos = $clientesModel->listarInformacionCajas();
    
            // Procesar datos para manejar valores NULL
            $datosProcesados = array_map(function($fila) {
                $fila['fecha_cierre'] = $fila['fecha_cierre'] ?? null; // Convertir NULL a null explícito
                $fila['valor_cierre'] = $fila['valor_cierre'] ?? null; // Convertir NULL a null explícito
                return $fila;
            }, $datos);
    
            echo json_encode(['tipo' => 'success', 'data' => $datosProcesados]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'addcart':
        $id_product = $_GET['id'];
        $product = $ventas->getProduct($id_product);
        if (!$product) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'PRODUCTO NO ENCONTRADO']);
            break;
        }

        if ($product['existencia'] <= 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'STOCK INSUFICIENTE']);
            break;
        }

        if (!isset($_SESSION['cart'][$id_user])) {
            $_SESSION['cart'][$id_user] = [];
        }

        if (!isset($_SESSION['cart'][$id_user][$id_product])) {
            $_SESSION['cart'][$id_user][$id_product] = ['cantidad' => 0, 'precio' => $product['precio_venta']];
        }

        $newQuantity = $_SESSION['cart'][$id_user][$id_product]['cantidad'] + 1;
        if ($newQuantity > $product['existencia']) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'STOCK INSUFICIENTE']);
            break;
        }

        $_SESSION['cart'][$id_user][$id_product]['cantidad'] = $newQuantity;
        echo json_encode(['tipo' => 'success', 'mensaje' => 'PRODUCTO AGREGADO AL CARRITO', 'producto' => $product]);
        break;

    case 'listarTemp':
        if (!isset($_SESSION['cart'][$id_user])) {
            $_SESSION['cart'][$id_user] = [];
        }

        $cartItems = $_SESSION['cart'][$id_user];
        $result = [];

        foreach ($cartItems as $id_product => $item) {
            $product = $ventas->getProduct($id_product);
            if ($product) {
                $product['cantidad'] = $item['cantidad'];
                $product['precio_venta'] = $item['precio'];
                $result[] = $product;
            }
        }

        echo json_encode($result);
        break;

    case 'addcantidad':
        $data = json_decode(file_get_contents('php://input'), true);
        $id_product = $data['id'];
        $cantidad = $data['cantidad'];

        if (isset($_SESSION['cart'][$id_user][$id_product])) {
            $_SESSION['cart'][$id_user][$id_product]['cantidad'] = $cantidad;

            $product = $ventas->getProduct($id_product);
            echo json_encode(['tipo' => 'success', 'mensaje' => 'CANTIDAD ACTUALIZADA', 'producto' => $product]);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'ERROR AL ACTUALIZAR CANTIDAD']);
        }
        break;

    case 'addprecio':
        $data = json_decode(file_get_contents('php://input'), true);
        $id_product = $data['id'];
        $precio = $data['precio'];

        if (isset($_SESSION['cart'][$id_user][$id_product])) {
            $_SESSION['cart'][$id_user][$id_product]['precio'] = $precio;

            $product = $ventas->getProduct($id_product);
            echo json_encode(['tipo' => 'success', 'mensaje' => 'PRECIO ACTUALIZADO', 'producto' => $product]);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'ERROR AL ACTUALIZAR PRECIO']);
        }
        break;

    case 'delete':
        $id_product = $_GET['id'];

        if (isset($_SESSION['cart'][$id_user][$id_product])) {
            unset($_SESSION['cart'][$id_user][$id_product]);

            // No aumentar el stock del producto cuando se elimina del carrito
            echo json_encode(['tipo' => 'success', 'mensaje' => 'PRODUCTO ELIMINADO DEL CARRITO']);
        } else {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'ERROR AL ELIMINAR PRODUCTO']);
        }
        break;

    case 'saveventa':
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (is_null($data)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Datos de entrada inválidos']);
            break;
        }
    
        $id_cliente = isset($data['idCliente']) ? $data['idCliente'] : null;
        $metodo = isset($data['metodo']) ? $data['metodo'] : null;
    
        if (is_null($id_cliente) || is_null($metodo)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Datos de cliente o método de pago no válidos']);
            break;
        }
    
        switch ($metodo) {
            case 'Efectivo':
                $metodo = 1;
                break;
            case 'Credito':
                $metodo = 3;
                break;
            case 'Bancaria':
                $metodo = 2;
                break;
            default:
                echo json_encode(['tipo' => 'error', 'mensaje' => 'Método de pago no válido']);
                exit;
        }
    
        if (!isset($_SESSION['cart'][$id_user])) {
            $_SESSION['cart'][$id_user] = [];
        }
    
        if (empty($_SESSION['cart'][$id_user])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'CARRITO VACIO']);
            break;
        }
    
        $total = 0;
        $stock_insuficiente = false;
    
        foreach ($_SESSION['cart'][$id_user] as $id_product => $item) {
            $product = $ventas->getProduct($id_product);
            if ($item['cantidad'] > $product['existencia']) {
                $stock_insuficiente = true;
                break;
            }
            $total += $item['precio'] * $item['cantidad'];
        }
    
        if ($stock_insuficiente) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'STOCK INSUFICIENTE']);
            break;
        }
    
        $cliente = $clientes->getClienteById($id_cliente);
        if (!$cliente) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'CLIENTE NO ENCONTRADO']);
            break;
        }
    
        if ($metodo == 3 && $cliente['capacidad'] < $total) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'CAPACIDAD DE CRÉDITO INSUFICIENTE']);
            break;
        }
    
        $fecha = date('Y-m-d'); // Captura la fecha actual en la zona horaria configurada
        $saleId = $ventas->saveVenta($id_cliente, $total, $metodo, $fecha, $id_user);
    
        // Obtener la sede del usuario desde la sesión
        $id_sede = $_SESSION['id_sede'];
    
        foreach ($_SESSION['cart'][$id_user] as $id_product => $item) {
            $ventas->saveDetalle($id_product, $saleId, $item['cantidad'], $item['precio'], $id_sede);
            $product = $ventas->getProduct($id_product);
            $stock = $product['existencia'] - $item['cantidad'];
            $ventas->updateStock($stock, $id_product);
        }
    
        // Solo actualizar la deuda y capacidad del cliente si el método de pago es 3 (Credito)
        if (isset($metodo) && $metodo == 3) {
            $clientes->updateDeudaCapacidad($id_cliente, $total, $metodo);
        }
    
        unset($_SESSION['cart'][$id_user]);
    
        echo json_encode(['tipo' => 'success', 'mensaje' => 'Venta guardada correctamente']);
        break;
        

    case 'searchbarcode':
        $barcode = $_GET['barcode'];
        $producto = $ventas->getBarcode($barcode);
    
        if (!$producto) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'PRODUCTO NO EXISTE']);
        } else {
            if (!isset($_SESSION['cart'][$id_user][$producto['id_producto']])) {
                $_SESSION['cart'][$id_user][$producto['id_producto']] = ['cantidad' => 0, 'precio' => $producto['precio_venta']];
            }
    
            $_SESSION['cart'][$id_user][$producto['id_producto']]['cantidad']++;
    
            echo json_encode([
                'tipo' => 'success',
                'mensaje' => 'Producto agregado correctamente',
                'producto' => $producto
            ]);
        }
        break;
                

    case 'listar-clientes':
        $result = $clientes->getClients();
        echo json_encode($result);
        break;
    
    case 'logout':
        // Destruir la sesión
        session_destroy();
    
        // Redirigir a la página principal
        header("Location: http://localhost/sistema-cafeteria-pulpafruit/");
        exit();
        break;
        

    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}
