function btnConfirmar(tipo){
    ajax('confirmarMovimiento',{tipo:tipo});
}

function confirmarMovimiento() {
    location.reload(true);
}