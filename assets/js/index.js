document.addEventListener('DOMContentLoaded', function () {
    const cajaAbierta = document.body.getAttribute('data-caja-abierta') === 'true';

    if (!cajaAbierta) {
        $('#modalAbrirCaja').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#modalAbrirCaja').modal('show');
    }

    document.querySelector('#formAperturaCaja').addEventListener('submit', function (e) {
        e.preventDefault();
    
        const valorApertura = document.querySelector('#valorApertura').value;
        const sede = document.querySelector('#sede').value;
    
        console.log('Datos que se envían:', {
            valorApertura: valorApertura,
            sede: sede
        });
    
        // Utilizar FormData para enviar los datos
        const formData = new FormData();
        formData.append('valorApertura', valorApertura);
        formData.append('sede', sede);
    
        axios.post(ruta + 'controllers/adminController.php?option=abrirCaja', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(function (response) {
            // Verificar la respuesta recibida
            console.log('Respuesta del servidor:', response);
            
            // Intentamos extraer el objeto JSON recibido
            if (response.data && response.data.tipo === 'success') {
                $('#modalAbrirCaja').modal('hide');
                alert(response.data.mensaje);  // Mostrar el mensaje de éxito correctamente
            } else {
                alert('Error: ' + response.data.mensaje);  // Mostrar el mensaje de error si lo hay
            }
        })
        .catch(function (error) {
            console.error('Error en la solicitud:', error);
        });
    });
    
    
    
});


