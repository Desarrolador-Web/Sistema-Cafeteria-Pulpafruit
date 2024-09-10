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



