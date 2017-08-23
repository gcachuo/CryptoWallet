$(function () {
    ajax('obtenerIdioma');
    if (typeof moneda !== 'undefined')
        notifyMe(moneda.nombre, moneda.ganancia + ' | ' + moneda.porcentaje + '%')
});

function obtenerIdioma(idioma) {
    cargarDatatable(idioma.datatable);
}

function btnEditar(simbolo) {
    aside('wallet', 'editar', {simbolo: simbolo});
}