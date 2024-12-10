<title>Panel Administrativo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="container">
    <h1 class="h3 mb-4 text-center">Panel Administrativo</h1>
    <div class="metrics-container">
        <!-- Inicios de Sesión -->
        <div class="metric-card" id="metric-inicios-sesion">
            <i class="fas fa-sign-in-alt metric-icon"></i>
            <div class="metric-title">Inicios de Sesión</div>
            <div class="metric-description">Visualiza los últimos inicios de sesión registrados por usuarios.</div>
        </div>

        <!-- Usuarios Registrados -->
        <div class="metric-card" id="metric-usuarios-registrados">
            <i class="fas fa-users metric-icon"></i>
            <div class="metric-title">Usuarios Registrados</div>
            <div class="metric-description">Revisa el listado de usuarios y sus roles en el sistema.</div>
        </div>
        <!-- Productos Agotados -->
        <div class="metric-card">
            <i class="fas fa-box-open metric-icon"></i>
            <div class="metric-title">Productos Agotados</div>
            <div class="metric-description">Consulta los productos que están actualmente agotados.</div>
        </div>
        <!-- Ventas del Mes -->
        <div class="metric-card">
            <i class="fas fa-chart-line metric-icon"></i>
            <div class="metric-title">Ventas del Mes</div>
            <div class="metric-description">Visualiza las ventas realizadas en el mes actual.</div>
        </div>
    </div>
</div>

<!-- Modal inicios de sesión -->
<div class="modal fade" id="modalIniciosSesion" tabindex="-1" aria-labelledby="modalIniciosSesionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Se ajusta el ancho con modal-xl -->
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalIniciosSesionLabel">Inicios de Sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>Usuario</th>
                            <th>Sede</th>
                            <th>Apertura</th>
                            <th>Hora Apertura</th>
                            <th>Cierre</th>
                            <th>Hora Cierre</th>
                        </tr>
                    </thead>
                    <tbody id="tablaIniciosSesionBody">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal usuarios registrados -->
<div class="modal fade" id="modalUsuariosRegistrados" tabindex="-1" aria-labelledby="modalUsuariosRegistradosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Se ajusta el ancho con modal-xl -->
        <div class="modal-content">
            <div class="modal-header bg-success text-white"> <!-- Mismo estilo de encabezado -->
                <h5 class="modal-title" id="modalUsuariosRegistradosLabel">Usuarios Registrados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover">
                    <thead class="table-success"> <!-- Mismo estilo de encabezado de tabla -->
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuariosBody">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
