/**
 * Created by Memo on 10/abr/2017.
 */

$(function () {
    /*MaskedInput({
     elm: document.getElementById('txtTelefono'), // select only by id
     format: '(___)___-____',
     separator: '()-'
     });*/
});

function btnRegistrar() {
    if (validarFormulario($("#frmAside"))) {
        ajax('registrarNuevoCliente');
    }
}

function registrarNuevoCliente() {
    navegar('transacciones');
}

function changeDist() {
    $("input[name=password]").prop('disabled', function (i, v) {
        return !v;
    }).parent().toggleClass("hidden");
    $("input[name=repassword]").prop('disabled', function (i, v) {
        return !v;
    }).parent().toggleClass("hidden");
}