<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Información de Cajas</title>
    <link href="<?php echo RUTA . 'assets/css/sb-admin-2.min.css'; ?>" rel="stylesheet">
    <link href="<?php echo RUTA . 'assets/css/snackbar.min.css'; ?>" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Información de Cajas</h1>
        <table class="table table-bordered mt-3" id="tablaClientes">
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>ID Sede</th>
                    <th>Fecha Apertura</th>
                    <th>Fecha Cierre</th>
                    <th>Valor Cierre</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos serán llenados dinámicamente por JavaScript -->
            </tbody>
        </table>
    </div>

    <script src="<?php echo RUTA . 'assets/vendor/jquery/jquery.min.js'; ?>"></script>
    <script src="<?php echo RUTA . 'assets/js/axios.min.js'; ?>"></script>
    <script>
        const ruta = '<?php echo RUTA; ?>';
    </script>
    <script src="<?php echo RUTA . 'assets/js/clientes.js'; ?>"></script>
</body>

</html>


