<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proveedores</h1>
</div>

<!-- Card para Datos de la Empresa -->
<form id="frmEmpresa" autocomplete="off">
    <div class="card mb-3 border-primary">
        <div class="card-header bg-primary text-white">
            Datos de la Empresa
        </div>
        <div class="card-body">
            <input type="hidden" id="id_empresa" name="id_empresa">
            <div class="row">
                <div class="col-md-4">
                    <label for="">NIT Empresa <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nit_empresa" name="nit_empresa" placeholder="NIT Empresa">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Razón Social <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                        </div>
                        <input type="text" class="form-control" id="razon_social_empresa" name="razon_social_empresa" placeholder="Razón Social Empresa">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Teléfono <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" class="form-control" id="telefono_empresa" name="telefono_empresa" placeholder="Teléfono Empresa">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Correo <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="correo_empresa" name="correo_empresa" placeholder="Correo Empresa">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Dirección<span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="text" class="form-control" id="direccion_empresa" name="direccion_empresa" placeholder="Dirección Empresa">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary" id="btn-save-empresa">Guardar Empresa</button>
        </div>
    </div>
</form>

<!-- Card para Datos del Proveedor -->
<form id="frmProveedor" autocomplete="off">
    <div class="card mb-3 border-success">
        <div class="card-header bg-success text-white">
            Datos del Proveedor
        </div>
        <div class="card-body">
            <input type="hidden" id="id_proveedor" name="id_proveedor">
            <div class="row">
                <div class="col-md-4">
                    <label for="">Empresa <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                        </div>
                        <select class="form-control" id="empresa_id" name="empresa_id">
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Nombres <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Apellidos <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Celular <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Correo <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success" id="btn-save-proveedor">Guardar Proveedor</button>
        </div>
    </div>
</form>

<!-- Tabla con estilo moderno -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Proveedores</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="table_proveedores" style="width: 100%;">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Razón Social</th>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Celular</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Contenido dinámico -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de la empresa -->
<div class="modal fade" id="modal-empresa" tabindex="-1" aria-labelledby="empresaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="empresaLabel">Detalles de la Empresa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Información de la Empresa</h5>
                <p><strong>NIT:</strong> <span id="empresa-nit"></span></p>
                <p><strong>Razón Social:</strong> <span id="empresa-razon"></span></p>
                <p><strong>Teléfono:</strong> <span id="empresa-telefono"></span></p>
                <p><strong>Correo:</strong> <span id="empresa-correo"></span></p>
                <p><strong>Dirección</strong> <span id="empresa-direccion"></span></p>
                <hr>
                <h5>Proveedores Asociados</h5>
                <ul id="lista-proveedores">
                    <!-- Lista de proveedores asociados cargada dinámicamente -->
                </ul>
            </div>
        </div>
    </div>
</div>
