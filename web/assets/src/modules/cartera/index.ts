import {Defaults} from "../../defaults";
import $ from 'jquery';
import 'datatables.net';
import numeral from 'numeral';
import toastr from 'toastr';

export class Cartera {
    private static totales = {
        costo: 0,
        actual: 0,
    };
    private static coins;
    private static table: DataTables.Api;

    static btnChangeLimit(idMoneda) {
        const limit = prompt('Ingrese un nuevo costo.');
        $.post('users/setCoinLimit', {
            user_token: $("#user_token").val(),
            limit,
            idMoneda
        }).done(({data}: ApiResponse<[]>) => {
            Cartera.table.ajax.reload();
        });
    }

    constructor() {
        setInterval(() => {
            Cartera.totales = {
                costo: 0,
                actual: 0,
            }
            Cartera.table.ajax.reload(this.initComplete, false);
        }, 60000);
    }

    initDatatable() {
        Cartera.table = $("table").DataTable({
            order: [[7, 'desc']],
            stateSave: false,

            ajax: {
                type: 'POST',
                url: 'users/fetchAmounts',
                dataSrc: ({data: {amounts}}: ApiResponse<{ amounts: { book, cantidad, costo, estadisticas, idMoneda, limite, moneda, porcentaje, precio, promedio, total }[] }>) => {
                    Cartera.totales = {
                        costo: 0,
                        actual: 0,
                    }
                    Cartera.coins = amounts;
                    return amounts;
                },
                data: {
                    user_token: $('#user_token').val()
                },

                error: (e, settings, message) => {
                    const {responseJSON}: { responseJSON?: ApiErrorResponse } = e;

                    if (responseJSON.code == 401) {
                        location.href = "login?logout=true"
                    }

                    Cartera.totales = {
                        costo: 0,
                        actual: 0,
                    }
                    Cartera.coins = [];
                    console.error('DataTables error: ', responseJSON.message, responseJSON.error);
                    Defaults.Alert(message, 'error');
                    return true;
                },
            },

            pageLength: 25,
            scrollX: false,
            processing: true,
            serverSide: false,
            responsive: true,
            searching: false,
            initComplete: this.initComplete,

            footerCallback(row, data) {
                let costo = 0;
                let actual = 0;
                data.map((row) => {
                    costo += +row['costo'];
                    actual += +row['total'];
                })
                $("#txtTotalCosto").val(numeral(costo).format('$0,0.00'));
                $("#txtTotalActual").val(numeral(actual).format('$0,0.00'));
                $("#txtTotalGP").val(`${numeral(actual - costo).format('$0,0.00')} (${numeral((actual - costo)/costo).format('0,0.00%')})`);
            },

            rowCallback: function (row, data, index) {
                if (data['cantidad'] <= 0 || numeral(data['total']).format('0.00') <= 0) {
                    $(row).hide();
                }
            },

            columnDefs: Defaults.global.dt.getColumns([
                {
                    responsivePriority: 1,
                    title: 'Moneda',
                    data: 'moneda',
                    render: (data, type, {idMoneda}) => {
                        if (type == 'display') {
                            return `<button class="btn btn-sm btn-link" onclick="btnOpenStatistics('${idMoneda}')">${data}</button>`;
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 2,
                    title: 'Cantidad',
                    data: 'cantidad',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('0.00000000');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Precio',
                    data: 'precio',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 4,
                    title: 'Rango',
                    data: 'estadisticas',
                    render: ({sell, buy}, type, {promedio, precio}) => {
                        if (type === 'display') {
                            let successBuy = sell > precio && buy > precio;
                            let successSell = precio > buy && precio > sell;
                            const textBuy = successBuy ? 'success' : '';
                            const textSell = successSell ? 'success' : '';
                            return `<span><span class="text-${textBuy}">${numeral(buy).format('$0,0.00')}</span> - <span class="text-${textSell}">${numeral(sell).format('$0,0.00')}</span></span>`;
                        }
                        return buy;
                    }
                },
                {
                    responsivePriority: 4,
                    title: 'DCA',
                    data: 'promedio',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 2,
                    title: 'Costo',
                    data: 'costo',
                    render: (data, type, {idMoneda, limite: {venta}}) => {
                        if (type === 'display') {
                            if (idMoneda !== 'mxn') {
                                data = venta ? venta : data;
                            }
                            return `<button onclick="btnChangeLimit('${idMoneda}')" class="btn btn-sm btn-link text-dark">${numeral(data).format('$0,0.00')}</button>`;
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Actual',
                    data: 'total',
                    render: (data, type, {porcentaje}) => {
                        if (type === 'display') {
                            return `<span class="text-${porcentaje >= 0 ? 'success' : 'danger'}">` + numeral(data).format('$0,0.00') + '</span>';
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Utilidad',
                    data: null,
                    render: (data, type, {idMoneda, total: actual, costo, porcentaje, limite: {venta}}) => {
                        costo = venta ? venta : costo;
                        data = actual - costo;
                        if (type === 'display') {
                            return `<button class="btn btn-sm btn-link text-${porcentaje >= 0 ? 'success' : 'danger'}" onclick="btnOpenCalc('${idMoneda}')"><span>${numeral(data/costo).format('0,0.00%')}</span></button><span class="text-${porcentaje >= 0 ? 'success' : 'danger'}"">(${numeral(data).format('$0,0.00')})</span>`;
                        }
                        return data/costo;
                    }
                }
            ]),
        });
    }

    initComplete() {
        Cartera.totales.costo = 0;
        Cartera.totales.actual = 0;

        $.post('users/fetchCoinLimits', {
            user_token: $("#user_token").val()
        }).done(({data: {sell}}: ApiResponse<{ sell }>) => {
            Cartera.autoSell(sell);
        });

        console.info('finish: ' + Date().toString());
    }

    static autoSell(sell) {
        $.each(sell, (key, val) => {
            const coin = Cartera.coins.find(function (element) {
                return element.idMoneda === key;
            });
            delete coin['estadisticas'];

            const threshold = +val.threshold;
            const amount = +val.amount;
            if (coin.total > (threshold + amount) && amount > 0) {
                const total = Math.floor((coin.total - threshold) / amount) * amount;
                if (isNaN(total)) {
                    console.error("Trying to sell NaN", coin.total, threshold, amount);
                    return;
                }
                Defaults.browserNotification({
                    title: coin.idMoneda.toUpperCase(),
                    body: 'Selling $' + total + ' ' + coin.idMoneda.toUpperCase()
                });
                toastr.info('Selling $' + total + ' ' + coin.idMoneda);
                console.info('Selling $' + total + ' ' + coin.idMoneda);
                $.post('users/sellCoin', {
                    coin, total,
                    user_token: $("#user_token").val()
                }).done((e: ApiResponse<boolean>) => {
                    if (!e.data) {
                        toastr.clear();
                        return;
                    }
                    Defaults.browserNotification({
                        title: coin.idMoneda.toUpperCase(),
                        body: 'Sold ' + total + ' ' + coin.idMoneda.toUpperCase()
                    });
                    toastr.success('Sold ' + total + ' ' + coin.idMoneda);
                    console.info('Sold ' + total + ' ' + coin.idMoneda);
                    Cartera.table.ajax.reload();
                }).fail(response => {
                    if (response.responseJSON) {
                        const {code, message, response: {message: error_message}}: ApiErrorResponse = response.responseJSON;
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

    static btnOpenStatistics(idMoneda) {
        Defaults.openModal({title: `Estadisticas | ${idMoneda.toUpperCase()}`, url: `estadisticas?coin=${idMoneda}`})
    }

    static btnOpenCalc(idMoneda) {
        Defaults.openModal({title: `Utilidad | ${idMoneda.toUpperCase()}`, url: `utilidad?coin=${idMoneda}`})
    }
}

window['btnChangeLimit'] = Cartera.btnChangeLimit;
window['btnOpenStatistics'] = Cartera.btnOpenStatistics;
window['btnOpenCalc'] = Cartera.btnOpenCalc;
