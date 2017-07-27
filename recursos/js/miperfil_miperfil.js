/**
 * Created by Memo on 27/abr/2017.
 */

function btnGuardar() {
    if (validarFormulario($("#frmAside")))
        ajax("guardarCambios", undefined, "miperfil");
}

function guardarCambios() {
    location.reload(true);
}