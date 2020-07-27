let tableClients, tableCoins, totales;
$(function () {
    if (!JSON.parse(localStorage.getItem('user'))) {
        location.href = 'login';
        return;
    }
    tableClients = $("#tabla-clientes").DataTable({
        order: [[2, 'desc']],
        ajax: {
            type: 'POST',
            url: 'users/fetchClients',
            dataSrc: ({status, code, response: {message, data: {clients, wallet}}, error}) => {
                totales = {
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
                        const cantidad = (totales.monedas.clientes[idMoneda] || 0) + Number(moneda);
                        const valor = (totales.valor.clientes[idMoneda] || 0) + Number(client.valor[idMoneda]);
                        totales.monedas.clientes[idMoneda] = Math.round(cantidad * 100000000) / 100000000;
                        totales.valor.clientes[idMoneda] = valor;
                    });
                    totales.actual.clientes += +client.total;
                    totales.costo.clientes += +client.costo;
                });
                return clients;
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
            ];
            columns.map((column, index) => {
                column['targets'] = index;
                return column;
            });
            return columns;
        })(),
    });
});

function initComplete() {
    totales.utilidad = {
        cartera: totales.actual.cartera - totales.costo.cartera,
        clientes: totales.actual.clientes - totales.costo.cartera,
    };
    const actualDiferencia = totales.actual.cartera - totales.actual.clientes;
    const costoDiferencia = totales.costo.cartera - totales.costo.clientes;
    const utilidadDiferencia = totales.utilidad.cartera - totales.utilidad.clientes;

    $("#txtActualCartera").val(numeral(totales.actual.cartera).format('$0,0.00'));
    $("#txtActualClientes").val(numeral(totales.actual.clientes).format('$0,0.00'));
    $("#txtActualDiferencia").val(numeral(actualDiferencia).format('$0,0.00'));

    $("#txtCostoCartera").val(numeral(totales.costo.cartera).format('$0,0.00'));
    $("#txtCostoClientes").val(numeral(totales.costo.clientes).format('$0,0.00'));
    $("#txtCostoDiferencia").val(numeral(costoDiferencia).format('$0,0.00'));

    $("#txtUtilidadCartera").val(numeral(totales.utilidad.cartera).format('$0,0.00'));
    $("#txtUtilidadClientes").val(numeral(totales.utilidad.clientes).format('$0,0.00'));
    $("#txtUtilidadDiferencia").val(numeral(utilidadDiferencia).format('$0,0.00'));

    const data = [];
    $.each(totales.monedas.cartera, (moneda, cartera) => {
        const clientes = totales.monedas.clientes[moneda] || 0;
        const diferencia = Math.round((cartera - clientes) * 100000000) / 100000000;
        const valor = ((totales.valor.cartera[moneda] || 0) - (totales.valor.clientes[moneda] || 0));
        data.push({moneda, cartera, clientes, diferencia, valor});
    });
    tableCoins = $("#tabla-monedas").DataTable({
        data,

        pageLength: 25,
        scrollX: false,
        processing: true,
        serverSide: false,
        responsive: true,
        paginate: false,
        searching: false,

        columnDefs: (() => {
            const columns = [
                {
                    responsivePriority: 1,
                    title: 'Moneda', data: 'moneda',
                    render: (data, type) => {
                        return data.toUpperCase();
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Cartera', data: 'cartera',
                    render: (data, type, {moneda}) => {
                        if (type === 'display') {
                            return `<span title="${numeral(totales.valor.cartera[moneda]).format('$0,0.00')}">` + numeral(data).format('0.00000000') + `</span>`;
                        }
                        return data;
                    }
                },
                {
                    responsivePriority: 1,
                    title: 'Clientes', data: 'clientes',
                    render: (data, type, {moneda}) => {
                        if (type === 'display') {
                            return `<span title="${numeral(totales.valor.clientes[moneda]).format('$0,0.00')}">` + numeral(data).format('0.00000000') + `</span>`;
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
                        totales.valor.total += data;
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

        initComplete: () => {
            console.log(totales.valor.total);
        }
    });
}
