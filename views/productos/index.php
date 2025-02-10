<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nueva compra</h1>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-proveedor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de registro de productos -->
<form id="frmProductos" enctype="multipart/form-data" autocomplete="off">
    <div class="card mb-2">
        <div class="card-body">
            <input type="hidden" id="id_product" name="id_product" required>
            <div class="row">
                <div class="col-md-3">
                    <label for="barcode">Barcode <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Barcode"
                        pattern="\d+" required>
                </div>
                <div class="col-md-5">
                    <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion"
                        placeholder="Descripción" pattern="[^ ]+" required>
                </div>
                <div class="col-md-4">
                    <label for="id_empresa">Proveedor <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_empresa" name="id_empresa" required>
                        <option value="">Seleccione un proveedor</option>
                        <!-- Las opciones se llenarán dinámicamente -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="precio_compra">Precio de Compra <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="precio_compra" name="precio_compra" placeholder="0"
                        pattern="\d+(\.\d{1,2})?" required>
                </div>
                <div class="col-md-3">
                    <label for="precio_venta">Precio de Venta <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="precio_venta" name="precio_venta" placeholder="0"
                        pattern="\d+(\.\d{1,2})?" required>
                </div>
                <div class="col-md-3">
                    <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cantidad" name="cantidad" placeholder="0" pattern="\d+"
                        required>
                </div>
                <div class="col-md-3">
                    <label for="imagen">Imagen del Producto <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="imagen" name="imagen" required>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="button" class="btn btn-success" data-estado="1" id="btn-Recibido">Recibido</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" style="width: 100%;" id="table_productos">
                <thead>
                    <tr>
                        <th scope="col">Id</th> 
                        <th scope="col">Barcode</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Precio de Compra</th>
                        <th scope="col">Precio de Venta</th>
                        <th scope="col">Proveedor</th>
                        <th scope="col">Imagen</th>
                        <th scope="col">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal definido en la vista -->
<div id="modalMensajeRol" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 300px; text-align: center;">
        <h5>Mensaje</h5>
        <p id="modalMensajeTexto">Mensaje predeterminado</p>
        
        <label for="selectSede">Seleccione la sede:</label>
        <select id="selectSede" style="width: 100%; margin: 10px 0; padding: 5px;">
            <option value="1">Planta Principal</option>
            <option value="2">Planta 2</option>
            <option value="3">CEDI</option>
        </select>

        <button id="guardarModal" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Guardar</button>
        <button id="cerrarModal" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
    </div>
</div>


<style>
    /* Estilos para el modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>