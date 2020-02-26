$(function () {
        if (!JSON.parse(localStorage.getItem('user'))) {
            location.href = 'login';
            return;
        } else {
            const user = JSON.parse(localStorage.getItem('user'));
        }
    }
);
