/**
 * Created by Memo on 21/feb/2017.
 */

$(function () {
    $("input").keypress(function (event) {
        if (event.which === 13) {
            event.preventDefault();
            btnIniciarSesion();
        }
    });
});

function btnIniciarSesion() {
    ajax('iniciarSesion');
}

function iniciarSesion(result) {
    if (result.cambiarPass) {
        aside("miperfil", "miperfil");
    }
    else
        navegar('inicio');
}