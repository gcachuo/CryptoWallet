$(function () {
    Project.url = 'http://dev.gcachuo.ml/api/';
    Project.init();
    if (!localStorage.getItem('user')) {
        Project.navigate('sign-in');
    }
    else {
        Project.navigate('dashboard');
    }
});