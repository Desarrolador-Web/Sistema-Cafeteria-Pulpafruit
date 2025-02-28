document.addEventListener('DOMContentLoaded', function () {
    // Función para actualizar la fecha y hora automáticamente
    function actualizarFechaHora() {
        const fecha = new Date();
        const formatoFecha = fecha.toLocaleDateString('es-ES');
        const formatoHora = fecha.toLocaleTimeString('es-ES');
        document.getElementById('datetime').value = `${formatoFecha} ${formatoHora}`;
    }

    // Actualizar fecha y hora al cargar la página
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);

    // Evento para el botón de abrir caja
    document.getElementById('abrirCaja').addEventListener('click', () => {
        const valorApertura = document.getElementById('valor').value;
        const fechaApertura = document.getElementById('datetime').value;

        // Validar que el valor de apertura no esté vacío o sea cero
        if (!valorApertura || valorApertura <= 0) {
            Swal.fire({
                title: 'Error',
                text: 'El valor de apertura es obligatorio y debe ser mayor a cero.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Crear el objeto de datos
        const data = {
            valorApertura: valorApertura,
            fechaApertura: fechaApertura
        };

        // Enviar los datos al controlador mediante fetch
        fetch(`${ruta}controllers/configuracionController.php?option=abrirCaja`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.tipo === 'success') {
                Swal.fire({
                    title: '¡Caja Abierta!',
                    text: data.mensaje,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.reload(); // Refresca la página automáticamente
                });
            } else {
                Swal.fire({
                    title: 'Error al abrir la caja',
                    text: data.mensaje,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al abrir la caja: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
});
