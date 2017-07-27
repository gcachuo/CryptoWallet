/**
 * Created by Memo on 08/mar/2017.
 */

$(function () {
    ajax('obtenerIdioma');
});

function obtenerIdioma(result) {
    idioma = result;
    cargarDatatable(idioma.datatable);
}

function btnEliminar(id) {
    if (confirm("¿Esta seguro de eliminar este perfil?")) {
        ajax('eliminarPerfil', {idPerfil: id});
    }
}

function eliminarPerfil() {
    navegar('perfiles');
}