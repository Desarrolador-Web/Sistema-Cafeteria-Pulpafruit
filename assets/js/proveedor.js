const frmProveedor = document.querySelector('#frmProveedor');
const frmEmpresa = document.querySelector('#frmEmpresa');
const nit_empresa = document.querySelector('#nit_empresa');
const razon_social_empresa = document.querySelector('#razon_social_empresa');
const telefono_empresa = document.querySelector('#telefono_empresa');
const correo_empresa = document.querySelector('#correo_empresa');
const direccion_empresa = document.querySelector('#direccion_empresa');
const empresa_id = document.querySelector('#empresa_id');
const btn_save_proveedor = document.querySelector('#btn-save-proveedor');
const btn_save_empresa = document.querySelector('#btn-save-empresa');

document.addEventListener('DOMContentLoaded', function () {
    loadEmpresas(); // Cargar las empresas en el select

    $('#table_proveedores').DataTable({
        ajax: {
            url: ruta + 'controllers/proveedorController.php?option=listar',
            dataSrc: ''
        },
        columns: [
            { 
                data: 'razon_social_empresa',
                render: function (data, type, row) {
                    return `<a href="#" onclick="viewEmpresa(${row.id_empresa})">${data}</a>`;
                }
            },
            { data: 'nombre_completo' }, 
            { data: 'celular' },
            { data: 'correo' },
            { data: 'accion' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
        },
        "order": [[0, 'desc']]
    });

    // Exportar a PDF
    document.getElementById('exportar-pdf').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.text("Lista de Proveedores", 14, 16);
        doc.setFontSize(10);
        doc.setTextColor(100);

        let table = document.getElementById('table_proveedores');
        let rows = [];

        for (let i = 0, row; row = table.rows[i]; i++) {
            let rowData = [];
            for (let j = 0, col; col = row.cells[j]; j++) {
                rowData.push(col.innerText);
            }
            rows.push(rowData);
        }

        doc.autoTable({
            head: [rows[0]],   // Las cabeceras de la tabla
            body: rows.slice(1),  // Los datos de la tabla
            startY: 20,
            theme: 'striped'
        });

        doc.save('lista_proveedores.pdf');
    });

    // Exportar a Excel
    document.getElementById('exportar-excel').addEventListener('click', function() {
        let table = document.getElementById('table_proveedores');
        let wb = XLSX.utils.table_to_book(table, { sheet: "Proveedores" });
        XLSX.writeFile(wb, 'lista_proveedores.xlsx');
    });

    frmProveedor.onsubmit = function (e) {
        e.preventDefault();
        if (empresa_id.value == '' || nombres.value == '' || apellidos.value == '' || celular.value == '' || correo.value == '') {
            message('error', 'TODO LOS CAMPOS CON * SON REQUERIDOS');
        } else {
            const frmData = new FormData(frmProveedor);
            axios.post(ruta + 'controllers/proveedorController.php?option=saveProveedor', frmData)
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
    };

    frmEmpresa.onsubmit = function (e) {
        e.preventDefault();
        if (nit_empresa.value == '' || razon_social_empresa.value == '' || telefono_empresa.value == '' || correo_empresa.value == '' || direccion_empresa.value == '') {
            message('error', 'TODO LOS CAMPOS CON * SON REQUERIDOS');
        } else {
            const frmData = new FormData(frmEmpresa);
            axios.post(ruta + 'controllers/proveedorController.php?option=saveEmpresa', frmData)
                .then(function (response) {
                    const info = response.data;
                    message(info.tipo, info.mensaje);
                    if (info.tipo == 'success') {
                        loadEmpresas(); // Recargar las empresas en el select
                        frmEmpresa.reset();
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    };
});

function loadEmpresas() {
    axios.get(ruta + 'controllers/proveedorController.php?option=listarEmpresas')
        .then(function (response) {
            let options = '<option value="">Seleccione una empresa</option>';
            response.data.forEach(function (empresa) {
                options += `<option value="${empresa.id_empresa}">${empresa.razon_social}</option>`;
            });
            empresa_id.innerHTML = options;
        })
        .catch(function (error) {
            console.log(error);
        });
}

function deleteProveedor(id) {
    Snackbar.show({
        text: 'Está seguro de eliminar',
        width: '475px',
        actionText: 'Sí eliminar',
        backgroundColor: '#FF0303',
        onActionClick: function (element) {
            axios.get(ruta + 'controllers/proveedorController.php?option=delete&id=' + id)
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

function editProveedor(id) {
    axios.get(ruta + 'controllers/proveedorController.php?option=edit&id=' + id)
        .then(function (response) {
            const info = response.data;
            empresa_id.value = info.id_empresa;
            nombres.value = info.nombres;
            apellidos.value = info.apellidos;
            celular.value = info.celular;
            correo.value = info.correo;
            id_proveedor.value = info.id_proveedor;
            btn_save_proveedor.innerHTML = 'Actualizar';
        })
        .catch(function (error) {
            console.log(error);
        });
}

function viewEmpresa(id_empresa) {
    axios.get(ruta + 'controllers/proveedorController.php?option=viewEmpresa&id=' + id_empresa)
        .then(function (response) {
            const empresa = response.data.empresa;
            const proveedores = response.data.proveedores;

            document.getElementById('empresa-nit').innerText = empresa.nit || '';
            document.getElementById('empresa-razon').innerText = empresa.razon_social || '';
            document.getElementById('empresa-telefono').innerText = empresa.telefono_empresa || '';
            document.getElementById('empresa-correo').innerText = empresa.correo_empresa || '';
            document.getElementById('empresa-direccion').innerText = empresa.direccion || '';

            let listaProveedores = '';
            proveedores.forEach(function (prov) {
                listaProveedores += `<li>${prov.nombres} ${prov.apellidos}</li>`;
            });
            document.getElementById('lista-proveedores').innerHTML = listaProveedores;

            $('#modal-empresa').modal('show');
        })
        .catch(function (error) {
            console.log(error);
        });
}
