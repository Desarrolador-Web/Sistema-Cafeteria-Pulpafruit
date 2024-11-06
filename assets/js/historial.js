let minDate, maxDate, table;

document.addEventListener('DOMContentLoaded', function () {
  // Añadir un input de búsqueda en cada columna del encabezado
  $('#table_ventas thead tr').clone(true).appendTo('#table_ventas thead');
  $('#table_ventas thead tr:eq(1) th').each(function (i) {
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

  // Inicializar DateTime Pickers
  minDate = new DateTime($('#desde'), {
    format: 'YYYY-MM-DD'
  });
  maxDate = new DateTime($('#hasta'), {
    format: 'YYYY-MM-DD'
  });

  // Inicializar DataTable
  table = $('#table_ventas').DataTable({
    ajax: {
      url: ruta + 'controllers/historialVentasController.php?option=historial',
      dataSrc: ''
    },
    columns: [
      { data: 'id_ventas' },
      { data: 'nombres' },
      { data: 'producto' },
      { data: 'cantidad' },  
      { data: 'precio_venta' },  
      { data: 'subtotal' },  
      { data: 'fecha' }
    ],
    orderCellsTop: true,
    fixedHeader: true,
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
    }
  });

  // Filtrar la tabla al cambiar las fechas
  $('#desde, #hasta').on('change', function () {
    table.draw();
  });

  // Función de filtrado por fechas
  $.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
      var min = minDate.val();
      var max = maxDate.val();
      var date = new Date(data[6]);

      if (
        (!min && !max) ||
        (!min && date <= new Date(max)) ||
        (new Date(min) <= date && !max) ||
        (new Date(min) <= date && date <= new Date(max))
      ) {
        return true;
      }
      return false;
    }
  );
});

document.getElementById('export_pdf').addEventListener('click', function () {
  exportTableToPDF();
});

function exportTableToPDF() {
  var pdfWindow = window.open('', '_blank');

  var style = `
      <style>
          table {width: 100%; border-collapse: collapse;}
          table, th, td {border: 1px solid black; padding: 8px; text-align: left;}
          th {background-color: #f2f2f2;}
      </style>
  `;

  var tableHTML = document.getElementById('table_ventas').outerHTML;

  var content = `
      <html>
      <head>
          <title>Historial de Ventas</title>
          ${style}
      </head>
      <body>
          <h1>Historial de Ventas</h1>
          ${tableHTML}
      </body>
      </html>
  `;

  pdfWindow.document.write(content);
  pdfWindow.document.close();
  pdfWindow.focus();
  pdfWindow.print();
  pdfWindow.close();
}
