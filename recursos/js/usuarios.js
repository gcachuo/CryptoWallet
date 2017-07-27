/**
 * Created by Memo on 23/feb/2017.
 */

var idioma;

$(function () {
    ajax('obtenerIdioma');
});

function obtenerIdioma(result) {
    idioma = result;
    cargarDatatable(idioma.datatable);
}

function btnEliminarUsuario(id) {
    var usuarios = idioma.usuarios;
    if (confirm(usuarios.alertEliminar)) {
        ajax('eliminarUsuario', {idUsuario: id});
    }
}

function eliminarUsuario() {
    navegar('usuarios');
}

function btnEditar() {
    ajax('editarUsuario');
}

function editarUsuario() {
    navegar('usuarios');
}