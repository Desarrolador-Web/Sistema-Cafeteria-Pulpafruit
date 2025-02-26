<!-- Bootstrap CSS -->
<link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">

<!-- Bootstrap JS (Asegúrate de que es el bundle, incluye Popper.js) -->
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

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
        <div class="metric-card" id="metric-productos-agotados">
            <i class="fas fa-box-open metric-icon"></i>
            <div class="metric-title">Productos Agotados</div>
            <div class="metric-description">Consulta los productos que están actualmente agotados.</div>
        </div>

        <!--  Nivelar inventario -->
        <div class="metric-card" id="metric-nivelar-inventario">
            <i class="fas fa-cogs metric-icon"></i>
            <div class="metric-title">Nivelar Inventario</div>
            <div class="metric-description">Ajusta la existencia y precios de los productos.</div>
        </div>

        <!--  Descargar informe de créditos -->
        <div class="metric-card" id="metric-descargar-informe">
            <i class="fas fa-file-download metric-icon"></i>
            <div class="metric-title">Descarga informe créditos</div>
            <div class="metric-description">Permite descargar y visualizar un listado del personal que tiene crédito pendiente.</div>
        </div>

    </div>
</div>

<!-- Modal Nivelar Inventario -->
<div class="modal fade" id="modalNivelarInventario" tabindex="-1" aria-labelledby="modalNivelarInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNivelarInventarioLabel">Nivelar Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>Código Producto</th>
                            <th>Descripción</th>
                            <th>Existencia</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaNivelarInventarioBody">
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

<!-- Modal inicios de sesión -->
<div class="modal fade" id="modalIniciosSesion" tabindex="-1" aria-labelledby="modalIniciosSesionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUsuariosRegistradosLabel">Usuarios Registrados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover">
                    <thead class="table-success">
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

<!-- Modal productos agotados -->
<div class="modal fade" id="modalProductosAgotados" tabindex="-1" aria-labelledby="modalProductosAgotadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Encabezado del modal con el mismo estilo -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalProductosAgotadosLabel">Productos Agotados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabla con los mismos estilos -->
                <table class="table table-striped table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>Producto</th>
                            <th>Última Fecha Compra</th>
                            <th>Última Fecha Venta</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductosAgotadosBody">
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

<!-- Modal descargar informe -->
<div class="modal fade" id="modalDescargarInforme" tabindex="-1" aria-labelledby="modalDescargarInforme" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalDescargarInformeLabel">Personal con crédito pendiente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Campo de búsqueda -->
                <input type="text" id="buscadorInforme" class="form-control mb-3" placeholder="Buscar por cédula o nombre...">
                
                <!-- Tabla de datos -->
                <table class="table table-striped table-hover" id="tablaInforme">
                    <thead class="table-success">
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Valor crédito</th>
                        </tr>
                    </thead>
                    <tbody id="tablaDescargarInforme">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button id="descargarPDF" class="btn btn-danger">Descargar PDF</button>
                <button id="descargarExcel" class="btn btn-success">Descargar Excel</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>



































































































