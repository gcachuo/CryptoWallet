const totales = {
    costo: 0,
    actual: 0,
};

let coins, table;

$(function () {
    if (!JSON.parse(localStorage.getItem('user'))) {
        location.href = 'login';
        return;
    }
    table = $("table").DataTable({
        order: [[6, 'desc']],

        ajax: {
            type: 'POST',
            url: 'api/users/fetchAmounts',
            dataSrc: ({status, code, response: {message, data: {amounts}}, error}) => {
                coins = amounts;
                return amounts;
            },
            data: {
                user: JSON.parse(localStorage.getItem('user'))
            }
        },

        pageLength: 25,
        scrollX: false,
        processing: true,
        serverSide: false,
        responsive: true,
        paginate: false,
        searching: false,
        initComplete,

        language: {
            search: "Buscar:",
            emptyTable: "No hay registros que consultar",
            lengthMenu: "Mostrar _MENU_ registros por pagina",
            info: "Mostrando pagina _PAGE_ de _PAGES_",
            loadingRecords: "...",
            processing: "<i class='fa fa-spin fa-spinner'></i>",
            paginate: {
                first: "Primero",
                last: "Ultimo",
                next: "Siguiente",
                previous: "Anterior"
            },
        },

        columnDefs: (() => {
            const columns = [
                {
                    responsivePriority: 1,
                    title: 'Moneda', data: 'moneda'
                },
                {
                    responsivePriority: 2,
                    title: 'Cantidad', data: 'cantidad',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('0.00000000');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 3,
                    title: 'Precio', data: 'precio',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 4,
                    title: 'Promedio', data: 'promedio',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Costo', data: 'costo',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        totales.costo += +data;
                        return data;
                    }
                },
                {
                    responsivePriority: 2,
                    title: 'Actual', data: 'total',
                    render: (data, type, {porcentaje}) => {
                        if (type === 'display') {
                            return `<span class="text-${porcentaje >= 0 ? 'success' : 'danger'}">` + numeral(data).format('$0,0.00') + '</span>';
                        }
                        totales.actual += +data;
                        return data;
                    }
                },
                {
                    responsivePriority: 3,
                    title: '%', data: 'porcentaje',
                    render: (data, type) => {
                        if (type === 'display') {
                            return `<span class="text-${data >= 0 ? 'success' : 'danger'}">` + numeral(data).format('0,0.00%') + '</span>';
                        }
                        return data;
                    }
                },
            ];
            columns.map((column, index) => {
                column['targets'] = index;
                return column;
            });
            return columns;
        })(),
    });
    setInterval(function () {
        table.ajax.reload(initComplete, false);
    }, 60000);
});

function initComplete() {
    $("#txtTotalCosto").val(numeral(totales.costo).format('$0,0.00'));
    $("#txtTotalActual").val(numeral(totales.actual).format('$0,0.00'));
    $("#txtTotalGP").val(numeral(totales.actual - totales.costo).format('$0,0.00'));

    totales.costo = 0;
    totales.actual = 0;

    $.post('api/users/fetchCoinLimits', {
        user: JSON.parse(localStorage.getItem('user'))
    }).done(({status, code, response: {message, data: {sell}}, error}) => {
        autoSell(sell);
    });

    console.info('finish: ' + Date().toString());
}

function autoSell(sell) {
    $.each(sell, function (key, val) {
        const coin = coins.find(function (element) {
            return element.idMoneda === key;
        });
        const threshold = +val.threshold;
        const amount = +val.amount;
        if (coin.total > (threshold + amount)) {
            const total = Math.floor((coin.total - threshold) / amount) * amount;
            toastr.info('Selling $' + total + ' ' + coin.idMoneda);
            console.info('Selling $' + total + ' ' + coin.idMoneda);
            $.post('api/users/sellCoin', {
                coin, total,
                user: JSON.parse(localStorage.getItem('user'))
            }).done(() => {
                toastr.success('Sold ' + total + ' ' + coin.idMoneda);
                console.info('Sold ' + total + ' ' + coin.idMoneda);
                table.ajax.reload();
            }).fail(response => {
                if (response.responseJSON) {
                    const {status, code, response: {message, error: {type, message: error_message, file, line}}} = response.responseJSON;
                    switch (true) {
                        case code >= 500:
                            toastr.error('An error ocurred.');
                            console.error(error_message, response.responseJSON);
                            break;
                        case code >= 400:
                            toastr.warning(message);
                            console.warn(message);
                            return;
                        default:
                            toastr.error('An error ocurred.');
                            console.error(response.responseJSON);
                            break;
                    }
                } else if (response.responseText) {
                    toastr.error('An error ocurred.');
                    console.error(`${response.responseText}`);
                }
            });
        }
    });
}
