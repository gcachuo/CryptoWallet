$(function () {
    ajax('obtenerIdioma');
    ajax('cargarGraficas');
    if (typeof moneda !== 'undefined')
        notifyMe(moneda.nombre, moneda.ganancia + ' | ' + moneda.porcentaje + '%')
});

function obtenerIdioma(idioma) {
    cargarDatatable(idioma.datatable);
}

/**
 * @param {{balance:object,colores:array}} data
 */
function cargarGraficas(data) {
    cargarDoughnut3("balanceChart", data.balance.values, data.balance.names, data.colores);
}

function btnEditar(simbolo) {
    aside('wallet', 'editar', {simbolo: simbolo});
}