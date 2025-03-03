document.addEventListener('DOMContentLoaded', function () {
  // Añadir un input en cada th del encabezado de la tabla para la búsqueda
  $('#table_compras thead tr').clone(true).appendTo('#table_compras thead');
  $('#table_compras thead tr:eq(1) th').each(function (i) {
    $(this).html('<input type="text" placeholder="Buscar" />');

    // Aplicar estilos directamente desde el JavaScript
    $('input', this).css({
      'width': '100%',
      'padding': '5px',
      'margin-top': '5px',
      'border': '1px solid #ccc',
      'border-radius': '4px',
      'font-size': '14px',
      'box-sizing': 'border-box'
    });

    // Añadir evento para buscar en cada columna
    $('input', this).on('keyup change', function () {
      if (table.column(i).search() !== this.value) {
        table.column(i).search(this.value).draw();
      }
    });
  });

  // Inicializar DataTable con la configuración existente
  table = $('#table_compras').DataTable({
    ajax: {
      url: ruta + 'controllers/historialComprasController.php?option=historial',
      type: 'POST',
      data: function(d) {
        d.id_sede = idSede; 
        d.id_usuario = idUsuario;
      },
      dataSrc: ''
    },    
    columns: [
      { data: 'id' },
      { data: 'nombre' },
      { data: 'producto' },
      { data: 'total' },
      { data: 'fecha' }
    ],
    orderCellsTop: true,
    fixedHeader: true,
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
    }
  });

  minDate = new DateTime($('#desde'), {
    format: 'YYYY-MM-DD'
  });
  maxDate = new DateTime($('#hasta'), {
    format: 'YYYY-MM-DD'
  });

  $('#desde, #hasta').on('change', function () {
    table.draw();
  });

  $.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
      let min = minDate.val();
      let max = maxDate.val();
      let date = new Date(data[4]);

      if (
        (min === null && max === null) ||
        (min === null && date <= new Date(max)) ||
        (min <= date && max === null) ||
        (min <= date && date <= new Date(max))
      ) {
        return true;
      }
      return false;
    }
  );
});
