document.addEventListener('DOMContentLoaded', function () {
    // Verifica si la caja está abierta desde el atributo data del body
    const cajaAbierta = document.body.getAttribute('data-caja-abierta') === 'true';

    // Si no hay caja abierta, mostrar el modal automáticamente
    if (!cajaAbierta) {
        $('#modalAbrirCaja').modal({
            backdrop: 'static',  // Evita cerrar el modal al hacer clic fuera
            keyboard: false      // Evita cerrar el modal con la tecla Esc
        });
        $('#modalAbrirCaja').modal('show');
    }

    // Manejar la apertura de caja
    document.querySelector('#formAperturaCaja').addEventListener('submit', function (e) {
        e.preventDefault();
    
        const valorApertura = document.querySelector('#valorApertura').value;
        const sede = document.querySelector('#sede').value;
    
        const formData = new FormData();
        formData.append('valorApertura', valorApertura);
        formData.append('sede', sede);
    
        fetch(ruta + 'controllers/adminController.php?option=abrirCaja', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.tipo === 'success') {
                $('#modalAbrirCaja').modal('hide');
                alert(data.mensaje);
            } else {
                alert('Error: ' + data.mensaje);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
        });
    });
    
});
