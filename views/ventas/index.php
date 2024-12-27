<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nueva venta</h1>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm rounded">
            <div class="card-body">

                <!-- Datos del Personal -->
                <div class="d-flex justify-content-between mt-3">
                    <h5>Datos del Personal</h5>
                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#modal-personal">
                        <i class="fas fa-search"></i> Seleccionar Personal
                    </button>
                </div>
                <hr>
                <div class="row">
                    <input type="hidden" id="id-personal" value="1">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-users"></i></span>
                            </div>
                            <input type="text" class="form-control border-0" id="nombre-personal" placeholder="Nombre" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-home"></i></span>
                            </div>
                            <input type="text" class="form-control border-0" id="area-personal" placeholder="Área" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-money-bill-alt"></i></span>
                            </div>
                            <input type="text" class="form-control border-0" id="capacidad-personal" placeholder="Capacidad" readonly>
                        </div>
                    </div>
                </div>

                <hr> <!-- Separador visual -->

                <!-- Tabla temporal de productos -->
                <div class="table-responsive">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control border-0" placeholder="Código de Barras" id="search">
                    </div>
                    <table class="table table-striped table-hover" id="table_temp" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Precio</th>
<<<<<<< HEAD
                                <th scope="col">Cant</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Acción</th>
=======
                                <th scope="col">Cantidad</th>
                                <th scope="col"></th>
                                <th></th>
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <!-- Campo de Total -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h5>Total:</h5>
                        <h5 id="total-venta">0</h5>
                    </div>

                    <!-- Método de Pago -->
                    <div class="col-md-12 mt-3">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-credit-card mr-1"></i> Método</span>
                            </div>
                            <select id="metodo" class="form-control border-0 mr-3">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Credito">Crédito</option>
                                <option value="Bancaria">Bancaria</option>
                            </select>

                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary shadow-sm" id="btn-guardar">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Tabla de Productos Disponibles -->
    <div class="col-md-7">
        <div class="card shadow-sm rounded">
            <div class="card-body">
=======
    <!--Modal biometrico -->
    <div id="sales-modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <form role="form" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-header " style="background-color:rgb(78, 115, 223); color:#ccc; ">
                    <h2 class="modal-title">Biometrico</h2>
                    <button type="button" class="close" data-dismiss="modal" style="cursor: pointer;">&times;</button>                    
                </div>
                <div class="modal-content">

                    <form>
                        <div class="form-group card-body" style="display:grid; place-items:center; margin: auto;">
                            <input class="card-img-top" type="hidden" name="idBio" id="idBio">
                            <video name="bio" id="bio" width="227" height="170" style="border:1px solid #ccc;" autoplay></video><br>
                            <canvas name="can" id="can" width="227" height="170" style="border:1px solid #ccc;"></canvas>
                        </div>
                        <div class="form-group card-body" style="display:flex; justify-content: center; align-items: center; gap: 10px;">
                            <button id="biometricc" name="biometricc" class="btn btn-outline-info">Tomar Biometrico</button>
                            <button id="accept" name="accept" class="btn btn-outline-success">Aceptar</button>
                            <button id="refresh" name="refresh" class="btn btn-outline-primary">Refrescar</button>
                            <button type="button" id="cancel" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                        </div>  
                    </form>
                </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table_venta" style="width: 100%;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Código de barras</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Precio</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Seleccionar cliente</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table_venta" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Código</th>
                                <th scope="col">Nombre</th>
<<<<<<< HEAD
                                <th scope="col">Stock</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Acción</th>
=======
                                <th scope="col">Cédula</th>
                                <th scope="col">Área</th>
                                <th scope="col">Capacidad</th>
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<<<<<<< HEAD
<!-- Modal de Selección de Personal -->
<div class="modal fade" id="modal-personal" tabindex="-1" aria-labelledby="modalPersonalLabel" aria-hidden="true">
=======
<!-- Modal para capturar fotografía
<div class="modal fade" id="modal-camera" tabindex="-1" aria-labelledby="cameraLabel" aria-hidden="true">
>>>>>>> eae77a48f863dceaf7a9bfa932cd8e5b7113d14f
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Seleccionar Personal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table_personal" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Área</th>
                                <th scope="col">Capacidad</th> 
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> -->