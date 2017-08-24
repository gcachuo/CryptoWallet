function btnEliminar(id) {
    ajax('eliminarOrden', {id: id});
}
function eliminarOrden(){
    location.reload(true);
}