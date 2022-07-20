import {Defaults} from "../../defaults";
import moment from "moment";
import numeral from 'numeral';
import $ from "jquery";

export class Utilidad {
    initDatatable(coin) {
        $('table').DataTable({
            searching: false,
            retrieve: true,
            stateSave: false,
            order: [[0, 'desc']],
            ajax: {
                url: 'trades/profit',
                dataSrc: Defaults.global.dt.ajax.dataSrc('profit'),
                data: {coin}
            },
            columnDefs: Defaults.global.dt.getColumns([
                {
                    title: 'Fecha',
                    data: 'fecha',
                    render(data, type) {
                        if (type === 'display') {
                            return moment(data).format('DD/MMM/YYYY hh:mma');
                        }
                        return data;
                    }
                },
                {
                    title: 'Balance',
                    data: 'total_cantidad'
                },
                {
                    title: 'Precio',
                    data: 'precio',
                    render(data, type) {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    title: 'Valor',
                    data: 'total_actual',
                    render(data, type) {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    title: 'Movimiento',
                    data: 'moneda'
                },
                {
                    title: 'Total',
                    data: 'mxn',
                    render(data, type) {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    title: 'Porcentaje',
                    data: 'porcentaje',
                    render(data, type) {
                        if (type === 'display') {
                            return numeral(data).format('0,0.00%');
                        }
                        return data;
                    }
                },
            ]),

            footerCallback(node, data) {
                let
                    sells = 0,
                    buys = 0,
                    profit = 0;

                data.map((row: { mxn, total_actual }) => {
                    if (row.mxn > 0) {
                        buys += +row.mxn;
                    } else if (row.mxn < 0) {
                        sells += +row.mxn;
                    }
                    if (row.total_actual === 0) {
                        buys = sells = 0;
                    } else {
                        profit = row.total_actual - buys + Math.abs(sells);
                    }
                })

                $("#sells").text(numeral(Math.abs(sells)).format('$0,0.00'));
                $("#buys").text(numeral(buys).format('$0,0.00'));
                $("#profits").text(numeral(profit).format('$0,0.00'));
            },
        });
    }
}
