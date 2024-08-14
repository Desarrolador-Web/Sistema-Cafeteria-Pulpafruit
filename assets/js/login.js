const frm = document.querySelector('#frmLogin');
const cedula = document.querySelector('#cedula');
const password = document.querySelector('#password');

document.addEventListener('DOMContentLoaded', function() {
    frm.onsubmit = function(e) {
        e.preventDefault();
        if (cedula.value == '' || password.value == '') {
            message('error', 'TODO LOS CAMPOS CON * SON REQUERIDOS');
        } else {
            axios.post(ruta + 'controllers/usuariosController.php?option=acceso', {
                cedula: cedula.value,
                password: password.value
              })
              .then(function (response) {
                const info = response.data;
                if (info.tipo == 'success') {
                    window.location = ruta + 'plantilla.php';
                }
                message(info.tipo, info.mensaje);
              })
              .catch(function (error) {
                console.log(error);
              });
        }
    }
});

function message(tipo, mensaje) {
    Snackbar.show({
        text: mensaje,
        pos: 'top-right',
        backgroundColor: tipo == 'success' ? '#079F00' : '#FF0303',
        actionText: 'Cerrar'
    });
}
