function btnGuardar(id) {
    ajax('guardarCompra', {id: id});
}

function guardarCompra() {
    cerrarAside();
}

function buildTablaClientes(result) {
    $("#tablaClientes").html(result.tabla);
    cerrarAside();
}