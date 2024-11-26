<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nueva venta</h1>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm rounded">
            <div class="card-body">


                <div class="d-flex justify-content-between mt-3">
                    <h5>Datos del cliente</h5>
                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#modal-cliente"><i class="fas fa-search"></i> Seleccionar cliente</button>
                </div>
                <hr>
                <div class="row">
                    <input type="hidden" id="id-cliente" value="1">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-users"></i></span>
                            </div>
                            <input type="text" class="form-control border-0" id="nombre-cliente" placeholder="Nombre" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-home"></i></span>
                            </div>
                            <input type="text" class="form-control border-0" id="area-cliente" placeholder="Área" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-money-bill-alt"></i></span>
                            </div>
                            <input type="text" class="form-control border-0" id="capacidad-cliente" placeholder="Capacidad" readonly>
                        </div>
                    </div>
                </div>

                <hr> <!-- Agregué un separador para mejor organización visual -->

                <div class="table-responsive">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control border-0" placeholder="Barcode" id="search">
                    </div>
                    <table class="table table-striped table-hover" id="table_temp" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <!-- Campo de Total -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h5>Total:</h5>
                        <h5 id="total-venta">0</h5> <!-- Este valor se actualizará automáticamente -->
                    </div>

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
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table_clientes" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Cédula</th>
                                <th scope="col">Área</th>
                                <th scope="col">Capacidad</th>
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

<!-- Modal para capturar fotografía
<div class="modal fade" id="modal-camera" tabindex="-1" aria-labelledby="cameraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="cameraLabel">Capturar Fotografía</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <video id="video" width="100%" height="480" autoplay></video>
                <button id="snap" class="btn btn-primary btn-block shadow-sm">Capturar</button>
                <canvas id="canvas" style="display:none;"></canvas>
                <img id="photo" src="" style="display:none;" />
            </div>
        </div>
    </div>
</div> -->