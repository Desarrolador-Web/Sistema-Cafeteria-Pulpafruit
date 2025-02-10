document.addEventListener('DOMContentLoaded', function () {
    const barcodeInput = document.getElementById('barcode');
    const precioCompraInput = document.getElementById('precio_compra');
    const precioVentaInput = document.getElementById('precio_venta');
    const cantidadInput = document.getElementById('cantidad');
    const selectSede = document.getElementById('selectSede');
    const selectMetodo = document.getElementById('selectMetodo');
    const btnGuardar = document.getElementById('btn-Recibido');

    barcodeInput.addEventListener('input', function () {
        let barcode = barcodeInput.value.trim();
        if (barcode.length > 0) {
            buscarProductoPorBarcode(barcode);
        }
    });

    function buscarProductoPorBarcode(barcode) {
        axios.get(`controllers/comprasController.php?option=buscarProducto&barcode=${barcode}`)
            .then(response => {
                const data = response.data;
                if (data.existe) {
                    precioCompraInput.value = data.precio_compra;
                    precioVentaInput.value = data.precio_venta;
                } else {
                    precioCompraInput.value = '';
                    precioVentaInput.value = '';
                }
            })
            .catch(error => console.error('Error al buscar el producto:', error));
    }

    btnGuardar.addEventListener('click', function () {
        const barcode = barcodeInput.value.trim();
        const precio_compra = precioCompraInput.value.trim();
        const precio_venta = precioVentaInput.value.trim();
        const cantidad = cantidadInput.value.trim();
        const sede = selectSede.value;
        const metodo = selectMetodo.value;

        if (!barcode || !precio_compra || !precio_venta || !cantidad || !sede || !metodo) {
            Swal.fire('Error', 'Todos los campos son obligatorios.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('barcode', barcode);
        formData.append('precio_compra', precio_compra);
        formData.append('precio_venta', precio_venta);
        formData.append('cantidad', cantidad);
        formData.append('sede', sede);
        formData.append('metodo', metodo);

        axios.post('controllers/comprasController.php?option=registrarCompra', formData)
            .then(response => {
                const data = response.data;
                if (data.tipo === 'success') {
                    Swal.fire('Éxito', data.mensaje, 'success');
                    limpiarFormulario();
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            })
            .catch(error => console.error('Error al guardar la compra:', error));
    });

    function limpiarFormulario() {
        barcodeInput.value = '';
        precioCompraInput.value = '';
        precioVentaInput.value = '';
        cantidadInput.value = '';
        selectSede.value = '';
        selectMetodo.value = '';
    }
});
