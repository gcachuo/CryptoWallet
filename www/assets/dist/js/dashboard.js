var $loading = $(".loading");
var draw = 0;

$(function () {
    $("body > main > header").css('display', 'flex');
    const table = cargarTabla();
    Project.refreshInterval = setInterval(function () {
        table.ajax.reload()
    }, 60000);
});

function cargarTabla() {
    $loading.show();
    return $("#tableCoins").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        paginate: false,
        searching: false,
        columns: [{data: 'moneda'}, {
            data: 'cantidad',
            render: function (data, type, row) {
                return numeral(data).format('0.00000000');
            }
        }, {
            data: 'precio',
            render: function (data, type, row) {
                return numeral(data).format('$0,0.00');
            }
        }, {
            data: 'promedio',
            render: function (data, type, row) {
                return numeral(data).format('$0,0.00');
            }
        }, {
            data: 'costo',
            render: function (data, type, row) {
                return numeral(data).format('$0,0.00');
            }
        }, {
            data: 'total',
            render: function (data, type, row) {
                return numeral(data).format('$0,0.00');
            }
        }, {
            data: 'porcentaje',
            render: function (data, type, row) {
                return numeral(data).format('0,0.00%');
            }
        }],
        order: [[6, 'desc']],
        columnDefs: [],
        ajax: {
            url: localStorage.getItem('host') + 'api/' + 'users/fetchAmounts',
            data: {
                draw: draw,
                user: JSON.parse(localStorage.getItem('user'))
            },
            type: 'POST',
            dataSrc
        }
    });
}

function dataSrc(result) {
    result.recordsTotal = 10;
    result.recordsFiltered = 10;

    //console.log(result.response.amounts);
    const data = result.response.amounts;
    data.sort(function (a, b) {
        return b['porcentaje'] - a['porcentaje'];
    });
    const totales = {
        costo: 0,
        actual: 0
    };
    $.each(data, function (key, coin) {
        totales.costo += coin.costo * 1;
        totales.actual += coin.total;
    });
    $("#txtTotalCosto").val(numeral(totales.costo).format('$0,0.00'));
    $("#txtTotalActual").val(numeral(totales.actual).format('$0,0.00'));
    $("#txtTotalGP").val(numeral(totales.actual - totales.costo).format('$0,0.00'));

    console.log('finish: ' + Date().toString());
    $loading.hide();
    return data;
}