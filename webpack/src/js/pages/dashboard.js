var $loading;
var draw;

$(function () {
    localStorage.setItem('sell', JSON.stringify({
        btc: {threshold: 6000, amount: 100},
        eth: {threshold: 3000, amount: 100}
    }));
    $loading = $(".loading");
    draw = 0;
    $("body > main > header").css('display', 'flex');
    const table = cargarTabla();
    autoSell(table);
    timer({table});
});

function timer(param) {
    Project.refreshInterval = setInterval(function () {
        param.table.ajax.reload();
        autoSell(param.table);
    }, 60000);
}

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
            url: Project.host + 'api/users/fetchAmounts',
            data: {
                draw: draw++,
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
    localStorage.setItem('coins', JSON.stringify(data));
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

function autoSell(table) {
    const coins = JSON.parse(localStorage.getItem('coins'));
    const sell = JSON.parse(localStorage.getItem('sell'));
    $.each(sell, function (key, val) {
        const coin = coins.find(function (element) {
            return element.idMoneda === key;
        });
        if (coin.total > (val.threshold + val.amount)) {
            const total = Math.floor((coin.total - val.threshold) / val.amount) * val.amount;
            console.info('Selling ' + total + ' ' + coin.idMoneda);
            Project.request('coins/sellCoin', {
                coin, total,
                user: JSON.parse(localStorage.getItem('user'))
            }, 'POST').done(data => {
                console.info('Sold ' + total + ' ' + coin.idMoneda);
                table.ajax.reload();
            });
        }
    });
}