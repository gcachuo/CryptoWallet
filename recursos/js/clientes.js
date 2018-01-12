$(function () {
    ajax('obtenerIdioma');
});

function obtenerIdioma(idioma) {
    cargarDatatable(idioma.datatable);
}

function btnComprar(id) {
    aside('clientes', 'compra', {id: id});
}

function btnVender(id) {
    aside('clientes', 'venta', {id: id});
}

function btnRebalancear(id) {
    aside('clientes', 'rebalanceo', {id: id});
}