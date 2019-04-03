$(function () {
    Project.host = localStorage.getItem('host') || 'https://gcachuo.ml/cryptowallet/';
    Project.url = Project.host + 'api/';
    Project.init();

    if (!localStorage.getItem('user')) {
        Project.navigate('sign-in');
    } else {
        Project.navigate('dashboard');
        const user = JSON.parse(localStorage.getItem('user'));
        if (user.perfil === '0') {
            $(".clientes").show();
        }
    }

    Project.slideout = new Slideout({
        'panel': $("#panel").get(0),
        'menu': $("#menu").get(0),
        'padding': 256,
        'tolerance': 70
    });
    $(".toggle-button").on('click', function () {
        Project.slideout.toggle()
    });
});