$(function () {
    Project.host = localStorage.getItem('host') || 'http://gcachuo.ml/cryptowallet/';
    Project.url = Project.host + 'api/';
    Project.init();
    if (!localStorage.getItem('user')) {
        Project.navigate('sign-in');
    } else {
        Project.navigate('dashboard');
    }

    var slideout = new Slideout({
        'panel': $("#panel").get(0),
        'menu': $("#menu").get(0),
        'padding': 256,
        'tolerance': 70
    });
    $(".toggle-button").on('click', function () {
        slideout.toggle()
    });
});