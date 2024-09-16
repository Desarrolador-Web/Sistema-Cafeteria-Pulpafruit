<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Solo iniciar la sesión si aún no ha sido iniciada
}

$cajaAbierta = $_SESSION['caja_abierta'] ?? false;  // Si no está definida, asumimos que es false
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Panel de control</h1>
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

