const frm = document.querySelector('#frmUser');
const permiso = document.querySelector('#frmPermiso');
const cedula = document.querySelector('#cedula');
const nombres = document.querySelector('#nombres');
const apellidos = document.querySelector('#apellidos');
const correo = document.querySelector('#correo');
const clave = document.querySelector('#clave');
const id_user = document.querySelector('#id_user');
const btn_nuevo = document.querySelector('#btn-nuevo');
const btn_save = document.querySelector('#btn-save');
const ubicacion = document.querySelector('#ubicacion');


document.addEventListener('DOMContentLoaded', function () {
    $('#table_users').DataTable({
        ajax: {
            url: ruta + 'controllers/usuariosController.php?option=listar',
            dataSrc: ''
        },
        columns: [
            { data: 'id_usuario' },
            { data: 'nombre_completo' }, 
            { data: 'correo' },
            { data: 'sede_nombre' },
            { data: 'accion' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        },
        "order": [[0, 'desc']]
    });

    frm.onsubmit = function (e) {
        e.preventDefault();
        if (cedula.value == '' || nombres.value == '' || apellidos.value == '' || correo.value == '' || clave.value == '') {
            message('error', 'TODO LOS CAMPOS CON * SON REQUERIDOS');
        } else {
            const frmData = new FormData(frm);
            axios.post(ruta + 'controllers/usuariosController.php?option=save', frmData)
                .then(function (response) {
                    const info = response.data;
                    message(info.tipo, info.mensaje);
                    if (info.tipo == 'success') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }

    btn_nuevo.onclick = function () {
        frm.reset();
        id_user.value = '';
        btn_save.innerHTML = 'Guardar';
        clave.removeAttribute('readonly');
        nombres.focus();
    }

    permiso.onsubmit = function (e) {
        e.preventDefault();
        const frmData = new FormData(this);
        axios.post(ruta + 'controllers/usuariosController.php?option=savePermiso', frmData)
            .then(function (response) {
                const info = response.data;
                message(info.tipo, info.mensaje);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

});

function deleteUser(id) {
    Snackbar.show({
        text: 'Esta seguro de eliminar',
        width: '475px',
        actionText: 'Si eliminar',
        backgroundColor: '#FF0303',
        onActionClick: function (element) {
            axios.get(ruta + 'controllers/usuariosController.php?option=delete&id=' + id)
                .then(function (response) {
                    const info = response.data;
                    message(info.tipo, info.mensaje);
                    if (info.tipo == 'success') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    });
}

function editUser(id) {
    axios.get(ruta + 'controllers/usuariosController.php?option=edit&id=' + id)
        .then(function (response) {
            const info = response.data;
            id_user.value = info.id_usuario; 
            nombres.value = info.nombres;
            apellidos.value = info.apellidos;
            correo.value = info.correo;
            clave.value = '*********************';
            clave.setAttribute('readonly', 'readonly');
            btn_save.innerHTML = 'Actualizar';
        })
        .catch(function (error) {
            console.log(error);
        });
}


function permisos(id) {
    axios.get(ruta + 'controllers/usuariosController.php?option=permisos&id=' + id)
        .then(function (response) {
            const info = response.data;
            let html = '';
            info.permisos.forEach(permiso => {
                let accion = info.asig[permiso.id_permiso] ? 'checked' : '';
                html += `<div>
                    <label class="mb-2">
                        <input type="checkbox" name="permisos[]" value="${permiso.id_permiso}" ${accion}> ${permiso.nombre_permiso}
                    </label>
                    </div>`;
            });
            html += `<input name="id_usuario" type="hidden" value="${id}" />
            <button class="btn btn-outline-success float-right" type="submit">Guardar</button>`;
            permiso.innerHTML = html;
            $('#modalPermiso').modal('show');
        })
        .catch(function (error) {
            console.log(error);
        });
}
