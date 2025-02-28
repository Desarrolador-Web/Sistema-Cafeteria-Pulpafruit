<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Solo iniciar la sesión si aún no ha sido iniciada
}

$cajaAbierta = $_SESSION['caja_abierta'] ?? false;  // Si no está definida, asumimos que es false
?>
 
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cerrar Caja</h1>
</div>


<body data-caja-abierta="<?php echo $cajaAbierta ? 'true' : 'false'; ?>">


<div class="container-fluid p-0">
  <div class="row m-0">
    <div class="col-md-12 p-0">
      <div class="card shadow-lg border-0 w-100" style="max-width: 600px; margin: 20px auto; background: linear-gradient(135deg, #f6f8f9, #eef2f3);">
        <div class="card-header text-center bg-primary text-white p-2">
          <h5 class="mb-0">Cerrar Caja</h5>
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center p-3">
          <form id="formCerrarCaja" class="w-100 px-3">
            <!-- Valor de Cierre -->
            <div class="form-group mb-3">
              <label for="valorCierre" class="text-dark font-weight-bold">Valor de Cierre</label>
              <input type="number" class="form-control p-2" id="valorCierre" placeholder="Ingrese el valor de cierre" required>
            </div>
            <!-- Fecha de Cierre -->
            <div class="form-group mb-3">
              <label for="fechaCierre" class="text-dark font-weight-bold">Fecha</label>
              <input type="text" class="form-control p-2 bg-light" id="fechaCierre" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
            </div>
            <!-- Botón de Cerrar Caja -->
            <div class="text-center">
              <button type="submit" class="btn btn-success btn-lg w-100">Cerrar Caja</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Aquí solo se muestra el modal si no hay una caja abierta -->
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" aria-labelledby="modalAbrirCajaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAbrirCajaLabel">Apertura de Caja</h5>
      </div>
      <div class="modal-body">
          <form id="formAperturaCaja">
            <div class="form-group">
                <label for="valorApertura">Valor de Apertura</label>
                <input type="number" class="form-control" id="valorApertura" name="valorApertura" required>
            </div>
            <div class="form-group">
                <label for="fechaApertura">Fecha</label>
                <input type="text" class="form-control" id="fechaApertura" name="fechaApertura" value="<?php echo date('Y-m-d'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="sede">Seleccionar Sede</label>
                <select class="form-control" id="sede" name="sede" required>
                    <option value="1">Principal</option>
                    <option value="2">Planta 2</option>
                    <option value="3">CEDI</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Abrir Caja</button>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <h5 class="card-title">ARCHIVO PLANO DESCUENTOS CAFETERÍA</h5>
            <button class="btn btn-success" id="downloadExcel">
                Descargar Excel
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover" style="width: 100%;" id="table_compras">
                <thead>
                    <tr>
                        <th>CIA</th>
                        <th>CC EMPLEADO</th>
                        <th>AÑO</th>
                        <th>PERIODO</th>
                        <th>NOVEDAD</th>
                        <th>CODIGO</th>
                        <th>VLR</th>
                        <th>SALDO</th>
                        <th>OPERACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td></td>
                        <td>2025</td>
                        <td></td>
                        <td>TE</td>
                        <td>712</td>
                        <td></td>
                        <td>0</td>
                        <td>I</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>






<script>
document.getElementById('downloadExcel').addEventListener('click', function () {
    let table = document.getElementById('table_compras');
    let workbook = XLSX.utils.table_to_book(table, { sheet: "Compras" });
    XLSX.writeFile(workbook, 'compras.xlsx');
});

function cargarPersonalConDescuento() {
    fetch('controllers/adminController.php?option=obtenerPersonalConDescuento')
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del servidor:", data); // <-- Verificar en consola
            
            if (data.tipo === 'success' && data.datos.length > 0) {
                let tbody = document.querySelector("#table_compras tbody");
                tbody.innerHTML = ''; // Limpiar la tabla antes de insertar nuevos datos

                data.datos.forEach(persona => {
                    let fila = `
                        <tr>
                            <td>01</td>
                            <td>${persona.cedula}</td>
                            <td>2025</td>
                            <td>${persona.periodo}</td>
                            <td>TE</td>
                            <td>712</td>
                            <td>${persona.descuento}</td>
                            <td>0</td>
                            <td>I</td>
                        </tr>
                    `;
                    tbody.innerHTML += fila;
                });
            } else {
                console.error("No se encontraron datos:", data.mensaje);
            }
        })
        .catch(error => console.error("Error en la petición:", error));
}

// Llamar a la función al cargar la página
document.addEventListener("DOMContentLoaded", cargarPersonalConDescuento);




</script>

<!-- Script para exportar a Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
