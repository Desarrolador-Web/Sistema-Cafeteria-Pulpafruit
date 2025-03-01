<?php
require_once '../models/clientes.php';
require_once __DIR__ . "/../views/fpdf/fpdf.php";


$option = $_GET['option'] ?? '';
$clientesModel = new ClientesModel();

// Extender la clase FPDF para agregar encabezado en cada página
class PDF extends FPDF {     
    function Header() {
        // Título centrado
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(190, 10, "INFORME DESCUENTOS CAFETERIA", 0, 1, 'C');
        $this->Ln(5);

        // Leyenda
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(190, 6, utf8_decode("Al firmar el presente documento autorizo a la empresa PULPAFRUIT S.A, con Nit 800.164.351-6, para descontar de mi salario quincenalmente o en su defecto de mis prestaciones sociales en caso de retiro; el monto total adecuado a la fecha por consumos de cafetería establecidos en este documento. En caso de ser temporal autorizo descontarlos de mi cuenta de cobro."), 0, 'J');
        $this->Ln(5);

        // Encabezado de la tabla (alineado)
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 8, "Cedula", 1, 0, 'C'); // Reducido
        $this->Cell(80, 8, "Nombre", 1, 0, 'C'); // Aumentado
        $this->Cell(25, 8, "Deuda", 1, 0, 'C'); // Reducido
        $this->Cell(30, 8, "Firma", 1, 0, 'C');
        $this->Cell(30, 8, "A descontar", 1, 1, 'C'); // Aumentado
    }
}

switch ($option) {
    case 'iniciosSesion':
        try {
            $datos = $clientesModel->getIniciosSesion();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'usuariosRegistrados':
        try {
            $datos = $clientesModel->getUsuariosRegistrados();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'productosAgotados':
        try {
            $datos = $clientesModel->getProductosAgotados();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'nivelarInventario':
        try {
            $datos = $clientesModel->getNivelarInventario();
            echo json_encode(['tipo' => 'success', 'data' => $datos]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'actualizarProducto':
        try {
            $codigo_producto = $_POST['codigo_producto'];
            $existencia = $_POST['existencia'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
            $confirmacion = $_POST['confirmacion'] ?? false;
    
            // Llamar al modelo y pasar la confirmación
            $resultado = $clientesModel->actualizarProducto($codigo_producto, $existencia, $precio_compra, $precio_venta, $confirmacion);
    
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;

    case 'descargarInforme':
        try {
            $clientes = $clientesModel->getDescargarInforme(); 
    
            // Enviar solo JSON con los datos, sin generar el PDF
            echo json_encode(['tipo' => 'success', 'data' => $clientes]);
        } catch (Exception $e) {
            echo json_encode(['tipo' => 'error', 'mensaje' => $e->getMessage()]);
        }
        break;
        
    case 'generarInformePDF':
        try {
            $clientesModel = new ClientesModel();
            $clientes = $clientesModel->getDescargarInforme();

            // Crear PDF con nueva clase
            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 8); // Fuente más pequeña

            // Agregar datos a la tabla con las 5 columnas
            foreach ($clientes as $cliente) {
                $pdf->Cell(25, 8, $cliente['cedula'], 1);
                $pdf->Cell(80, 8, utf8_decode($cliente['nombre']), 1);
                $pdf->Cell(25, 8, "$" . number_format($cliente['deuda'], 2), 1, 0, 'R'); // Formato de dinero
                $pdf->Cell(30, 8, "", 1); // Celda vacía para firma
                $pdf->Cell(30, 8, "", 1); // Celda vacía para "A descontar"
                $pdf->Ln();
            }

            // Configurar encabezados HTTP para la descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Informe_Quincenal_Cafeteria.pdf"');

            // Descargar PDF
            $pdf->Output("D", "Informe_Quincenal_Cafeteria.pdf");
            exit;
        } catch (Exception $e) {
            die("Error al generar el PDF: " . $e->getMessage());
        }
        break;
    
    default:
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Opción no válida']);
        break;
}


