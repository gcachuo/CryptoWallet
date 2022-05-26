import {Defaults} from "../../defaults";
import $ from 'jquery';
import 'datatables.net';
import numeral from 'numeral';

export class Clientes {
    private static tableClients;
    private static tableCoins;
    private static totales;

    initDatatable() {
        Clientes.tableClients = $("#tabla-clientes").DataTable({
            order: [[2, 'desc']],
            ajax: {
                type: 'POST',
                url: 'users/fetchClients',
                data: {
                    user_token: $("#user_token").val()
                },
                dataSrc: ({data: {clients, wallet}}: ApiResponse<{ clients, wallet }>) => {
                    Clientes.totales = {
                        actual: {
                            cartera: wallet.total,
                            clientes: 0,
                        },
                        monedas: {cartera: wallet.monedas, clientes: {}},
                        valor: {cartera: wallet.valor, clientes: {}, total: 0},
                        costo: {
                            cartera: wallet.cost,
                            clientes: 0,
                        },
                    };
                    $.each(clients, function (key, client) {
                        $.each(client.monedas, function (idMoneda, moneda) {
                            const cantidad = (Clientes.totales.monedas.clientes[idMoneda] || 0) + Number(moneda);
                            const valor = (Clientes.totales.valor.clientes[idMoneda] || 0) + Number(client.valor[idMoneda]);
                            Clientes.totales.monedas.clientes[idMoneda] = Math.round(cantidad * 100000000) / 100000000;
                            Clientes.totales.valor.clientes[idMoneda] = valor;
                        });
                        Clientes.totales.actual.clientes += +client.total;
                        Clientes.totales.costo.clientes += +client.costo;
                    });
                    return clients;
                }
            },

            scrollX: false,
            processing: true,
            serverSide: false,
            searching: false,
            initComplete: this.initComplete,

            columnDefs: Defaults.global.dt.getColumns([
                {
                    responsivePriority: 1,
                    title: 'Nombre', data: 'nombre'
                },
                {
                    responsivePriority: 1,
                    title: 'Costo', data: 'costo',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Total', data: 'total',
                    render: (data, type) => {
                        if (type === 'display') {
                            return numeral(data).format('$0,0.00');
                        }
                        return data;
                    }
                },
            ]),
        });
    }

    initComplete() {
        Clientes.totales.utilidad = {
            cartera: Clientes.totales.actual.cartera - Clientes.totales.costo.cartera,
            clientes: Clientes.totales.actual.clientes - Clientes.totales.costo.cartera,
        };
        const actualDiferencia = Clientes.totales.actual.cartera - Clientes.totales.actual.clientes;
        const costoDiferencia = Clientes.totales.costo.cartera - Clientes.totales.costo.clientes;
        const utilidadDiferencia = Clientes.totales.utilidad.cartera - Clientes.totales.utilidad.clientes;

        $("#txtActualCartera").val(numeral(Clientes.totales.actual.cartera).format('$0,0.00'));
        $("#txtActualClientes").val(numeral(Clientes.totales.actual.clientes).format('$0,0.00'));
        $("#txtActualDiferencia").val(numeral(actualDiferencia).format('$0,0.00'));

        $("#txtCostoCartera").val(numeral(Clientes.totales.costo.cartera).format('$0,0.00'));
        $("#txtCostoClientes").val(numeral(Clientes.totales.costo.clientes).format('$0,0.00'));
        $("#txtCostoDiferencia").val(numeral(costoDiferencia).format('$0,0.00'));

        $("#txtUtilidadCartera").val(numeral(Clientes.totales.utilidad.cartera).format('$0,0.00'));
        $("#txtUtilidadClientes").val(numeral(Clientes.totales.utilidad.clientes).format('$0,0.00'));
        $("#txtUtilidadDiferencia").val(numeral(utilidadDiferencia).format('$0,0.00'));

        const data = [];
        $.each(Clientes.totales.monedas.cartera, (moneda, cartera) => {
            const clientes = Clientes.totales.monedas.clientes[moneda] || 0;
            const diferencia = Math.round((cartera - clientes) * 100000000) / 100000000;
            const valor = ((Clientes.totales.valor.cartera[moneda] || 0) - (Clientes.totales.valor.clientes[moneda] || 0));
            data.push({moneda, cartera, clientes, diferencia, valor});
        });
        Clientes.tableCoins = $("#tabla-monedas").DataTable({
            data,

            ajax: null,
            scrollX: false,
            processing: true,
            serverSide: false,
            searching: false,

            columnDefs: Defaults.global.dt.getColumns([
                {
                    responsivePriority: 1,
                    title: 'Moneda', data: 'moneda',
                    render: (data) => {
                        return data.toUpperCase();
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Cartera', data: 'cartera',
                    render: (data, type, {moneda}) => {
                        if (type === 'display') {
                            return `<span title="${numeral(Clientes.totales.valor.cartera[moneda]).format('$0,0.00')}">` + numeral(data).format('0.00000000') + `</span>`;
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Clientes', data: 'clientes',
                    render: (data, type, {moneda}) => {
                        if (type === 'display') {
                            return `<span title="${numeral(Clientes.totales.valor.clientes[moneda]).format('$0,0.00')}">` + numeral(data).format('0.00000000') + `</span>`;
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Diferencia', data: 'diferencia',
                    render: (data, type) => {
                        if (type === 'display') {
                            return `<span class="text-${data >= 0 ? 'success' : 'danger'}">` + numeral(data).format('0.00000000') + '</span>';
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Valor', data: 'valor',
                    render: (data, type) => {
                        if (type === 'display') {
                            return `<span class="text-${data >= 0 ? 'success' : 'danger'}">` + numeral(data).format('$0,0.00') + '</span>';
                        }
                        Clientes.totales.valor.total += data;
                        return data;
                    }
                },
            ])
        });
    }
}
