$(function () {
    ajax('obtenerIdioma');
});

function obtenerIdioma(idioma) {
    cargarDatatable(idioma.datatable);
}

function btnEditar(simbolo) {
    aside('wallet', 'editar', {simbolo: simbolo});
}