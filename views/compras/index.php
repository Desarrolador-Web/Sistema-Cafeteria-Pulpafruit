

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nueva compra</h1>
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
                    <label for="selectSede">Seleccione la sede:</label>
                    <select class="form-control" id="selectSede">
                        <option value="1">Planta Principal</option>
                        <option value="2">Planta 2</option>
                        <option value="3">CEDI</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="selectMetodo">Seleccione el método:</label>
                    <select class="form-control" id="selectMetodo">
                        <option value="1">Caja</option>
                        <option value="2">Socio</option>
                        <option value="3">Bancaria</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="button" class="btn btn-success" data-estado="1" id="btn-Recibido">Guardar</button>
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


