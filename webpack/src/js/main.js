$(function () {
    Project.host = localStorage.getItem('host') || 'http://gcachuo.ml/cryptowallet/';
    Project.url = Project.host + 'api/';
    Project.init();
    if (!localStorage.getItem('user')) {
        Project.navigate('sign-in');
    } else {
        Project.navigate('dashboard');
    }
});