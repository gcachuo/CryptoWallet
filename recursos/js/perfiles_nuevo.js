/**
 * Created by Memo on 06/mar/2017.
 */

$(function () {
    cargarNestable();
});

function btnGuardar() {
    ajax('guardarNuevoPerfil');
}

function guardarNuevoPerfil() {
    navegar('perfiles');
}

function btnEditar() {
    ajax('editarPerfil');
}

function editarPerfil() {
    navegar('perfiles');
}