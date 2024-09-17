document.addEventListener('DOMContentLoaded', function () {
    // Inicializar las funciones al cargar el DOM
    verificarCajaAbierta();
    manejarAperturaCaja();
    manejarCierreCaja();
});

// Función para verificar si el usuario tiene una caja abierta hoy
function verificarCajaAbierta() {
    fetch(ruta + 'controllers/adminController.php?option=verificarCaja')
        .then(response => response.json())
        .then(data => {
            if (!data.cajaAbierta) {
                mostrarModalAbrirCaja();
            }
        })
        .catch(error => {
            console.error('Error al verificar la caja:', error);
        });
}

// Función para mostrar el modal de apertura de caja
function mostrarModalAbrirCaja() {
    $('#modalAbrirCaja').modal({
        backdrop: 'static',  // Evita cerrar el modal al hacer clic fuera
        keyboard: false      // Evita cerrar el modal con la tecla Esc
    });
    $('#modalAbrirCaja').modal('show');
}

// Función para manejar la apertura de caja
function manejarAperturaCaja() {
    document.querySelector('#formAperturaCaja').addEventListener('submit', function (e) {
        e.preventDefault();

        const valorApertura = document.querySelector('#valorApertura').value;
        const sede = document.querySelector('#sede').value;

        const formData = new FormData();
        formData.append('valorApertura', valorApertura);
        formData.append('id_sede', sede);

        fetch(ruta + 'controllers/adminController.php?option=abrirCaja', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.tipo === 'success') {
                $('#modalAbrirCaja').modal('hide');
                Swal.fire({
                    title: '¡Caja abierta!',
                    text: data.mensaje,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error: ' + data.mensaje,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error en la solicitud: ' + error,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
}

// Función para manejar el cierre de caja
function manejarCierreCaja() {
    document.querySelector('#formCerrarCaja').addEventListener('submit', function (e) {
        e.preventDefault();

        const valorCierre = document.querySelector('#valorCierre').value;

        const formData = new FormData();
        formData.append('valorCierre', valorCierre);

        fetch(ruta + 'controllers/adminController.php?option=cerrarCaja', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.tipo === 'success') {
                Swal.fire({
                    title: '¡Caja cerrada!',
                    text: data.mensaje,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Limpiar el formulario de cierre de caja
                    document.querySelector('#formCerrarCaja').reset();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error: ' + data.mensaje,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error en la solicitud: ' + error,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
}
