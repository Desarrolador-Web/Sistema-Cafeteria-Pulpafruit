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

    document.getElementById('abrirCaja').addEventListener('click', () => {
        const valorApertura = document.getElementById('valor').value;
        const fechaApertura = document.getElementById('datetime').value;
        const idSede = document.getElementById('sede').value;
    
        // Crear el objeto de datos de manera explícita
        const data = {
            valorApertura: valorApertura,
            fechaApertura: fechaApertura,
            idSede: idSede
        };
    
        console.log("Enviando datos al backend:", JSON.stringify(data)); // Log para depuración
    
        // Enviar los datos al controlador mediante fetch
        fetch(`${ruta}controllers/configuracionController.php?option=abrirCaja`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data) // Asegurar que el JSON es correcto
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del backend:", data); // Log para verificar la respuesta
            if (data.tipo === 'success') {
                Swal.fire({
                    title: '¡Caja Abierta!',
                    text: data.mensaje,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.reload();
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
            console.error("Error en la solicitud:", error);
            Swal.fire({
                title: 'Error',
                text: 'Error al abrir la caja: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
})    