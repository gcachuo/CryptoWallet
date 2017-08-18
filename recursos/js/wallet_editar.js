function btnConfirmar(idUsuario, idMoneda) {
    ajax('editarUsuarioMoneda', {idUsuario: idUsuario, idMoneda: idMoneda})
}

function editarUsuarioMoneda() {
    cerrarAside();
    location.reload(true);
}