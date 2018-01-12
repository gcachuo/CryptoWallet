$(function () {
    ajax('obtenerIdioma');
});

function obtenerIdioma(idioma) {
    cargarDatatable(idioma.datatable);
}