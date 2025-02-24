<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarjeta con Valor y Fecha/Hora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Abrir Caja</h1>
</div>

    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-md-12 p-0">
                <div class="card shadow-lg border-0 w-100" style="max-width: 600px; margin: 50px auto; background: linear-gradient(135deg, #f6f8f9, #eef2f3);">
                    <div class="card-header text-center bg-success text-white p-2">
                        <h5 class="mb-0">Abrir Caja</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center p-3">
                        <form id="formAbrirCaja" class="w-100 px-3">
                            <!-- Valor de Apertura -->
                            <div class="form-group mb-3">
                                <label for="valor" class="text-dark font-weight-bold">Valor de Apertura</label>
                                <input type="number" class="form-control p-2" id="valor" placeholder="Ingrese el valor de apertura" required>
                            </div>
                            <!-- Fecha de Apertura -->
                            <div class="form-group mb-3">
                                <label for="fecha" class="text-dark font-weight-bold">Fecha</label>
                                <input type="text" class="form-control p-2 bg-light" id="datetime" 
                                    style="pointer-events: none; user-select: none; outline: none; border: none; background: #f8f9fa;" disabled>
                            </div>
                            <!-- Botón de Abrir Caja -->
                            <div class="text-center">
                                <button type="button" class="btn btn-success btn-lg w-100" id="abrirCaja">Abrir Caja</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para actualizar fecha y hora
        function actualizarFechaHora() {
            const fecha = new Date();
            const formatoFecha = fecha.toLocaleDateString('es-ES');
            const formatoHora = fecha.toLocaleTimeString('es-ES');
            document.getElementById('datetime').value = `${formatoFecha} ${formatoHora}`;
        }
        
        // Actualizar fecha y hora al cargar la página
        actualizarFechaHora();
        setInterval(actualizarFechaHora, 1000);
        
        // Evento del botón Abrir Caja
        document.getElementById('abrirCaja').addEventListener('click', () => {
            alert('Caja abierta correctamente');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $ruta; ?>assets/js/configuracion.js"></script>

</body>
</html>
