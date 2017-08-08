function btnGuardar() {
    if (validarFormulario($("#frmAside"))) {
        ajax('cambiarLlavesApi');
    }
}

function cambiarLlavesApi() {
    cerrarAside();
}