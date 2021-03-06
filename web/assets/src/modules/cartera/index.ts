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
    private static table;

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
            order: [[6, 'desc']],

            ajax: {
                type: 'POST',
                url: 'users/fetchAmounts',
                dataSrc: ({status, code, response: {message, data: {amounts}}, error}) => {
                    Cartera.coins = amounts;
                    return amounts;
                },
                data: {
                    user_token: $('#user_token').val()
                }
            },

            pageLength: 25,
            scrollX: false,
            processing: true,
            serverSide: false,
            responsive: true,
            searching: false,
            initComplete: this.initComplete,

            rowCallback: function (row, data, index) {
                if (data['cantidad'] <= 0) {
                    $(row).hide();
                }
            },

            columnDefs: Defaults.global.dt.getColumns([
                {
                    responsivePriority: 1,
                    title: 'Moneda', data: 'moneda',
                    render: (data, type, {idMoneda}) => {
                        if (type == 'display') {
                            return `<a class="btn btn-xs btn-link" href="estadisticas?coin=${idMoneda}">${data}</a>`
                        }
                        return data;
                    }
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
                    responsivePriority: 1,
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
                    responsivePriority: 2,
                    title: 'Costo', data: 'costo',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        Cartera.totales.costo += +data;
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Actual', data: 'total',
                    render: (data, type, {porcentaje}) => {
                        if (type === 'display') {
                            return `<span class="text-${porcentaje >= 0 ? 'success' : 'danger'}">` + numeral(data).format('$0,0.00') + '</span>';
                        }
                        Cartera.totales.actual += +data;
                        return data;
                    }
                },
                {
                    responsivePriority: 3,
                    title: 'Utilidad',
                    render: (data, type, {total: actual, costo, porcentaje}) => {
                        data = actual - costo;
                        if (type === 'display') {
                            return `<span class="text-${porcentaje >= 0 ? 'success' : 'danger'}">` + numeral(data).format('$0,0.00') + '</span>';
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 3,
                    title: '%', data: 'porcentaje',
                    render: (data, type) => {
                        if (type === 'display') {
                            if (data !== null) {
                                return `<span class="text-${data >= 0 ? 'success' : 'danger'}">` + numeral(data).format('0,0.00%') + '</span>';
                            }
                            return `<span class="text-muted">N/A</span>`;
                        }
                        return data;
                    }
                },
            ]),
        });
    }

    initComplete() {
        $("#txtTotalCosto").val(numeral(Cartera.totales.costo).format('$0,0.00'));
        $("#txtTotalActual").val(numeral(Cartera.totales.actual).format('$0,0.00'));
        $("#txtTotalGP").val(numeral(Cartera.totales.actual - Cartera.totales.costo).format('$0,0.00'));

        Cartera.totales.costo = 0;
        Cartera.totales.actual = 0;

        $.post('users/fetchCoinLimits', {
            user_token: $("#user_token").val()
        }).done(({status, code, response: {message, data: {sell}}, error}) => {
            Cartera.autoSell(sell);
        });

        console.info('finish: ' + Date().toString());
    }

    static autoSell(sell) {
        $.each(sell, (key, val) => {
            const coin = Cartera.coins.find(function (element) {
                return element.idMoneda === key;
            });
            const threshold = +val.threshold;
            const amount = +val.amount;
            if (coin.total > (threshold + amount)) {
                const total = Math.floor((coin.total - threshold) / amount) * amount;
                toastr.info('Selling $' + total + ' ' + coin.idMoneda);
                console.info('Selling $' + total + ' ' + coin.idMoneda);
                $.post('users/sellCoin', {
                    coin, total,
                    user_token: $("#user_token").val()
                }).done(() => {
                    toastr.success('Sold ' + total + ' ' + coin.idMoneda);
                    console.info('Sold ' + total + ' ' + coin.idMoneda);
                    Cartera.table.ajax.reload();
                }).fail(response => {
                    if (response.responseJSON) {
                        const {
                            status,
                            code,
                            response: {message, error: {type, message: error_message, file, line}}
                        } = response.responseJSON;
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
}
