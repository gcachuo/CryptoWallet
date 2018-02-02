function btnGuardar(id) {
    ajax('guardarVenta', {id: id});
}

function guardarVenta() {
    cerrarAside();
}

function buildTablaClientes(result) {
    $("#tablaClientes").html(result.tabla);
    cerrarAside();
}