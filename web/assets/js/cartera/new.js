$(function () {
        if (!JSON.parse(localStorage.getItem('user'))) {
            location.href = 'login';
            return;
        }
    }
);
