$(function () {
    localStorage.removeItem('user');
    $("form").submit(function (e) {
        e.preventDefault();

        var data = $(e.currentTarget).serializeArray();

        $.post("api/users/signIn", data).done(({status, code, response: {message, data: {user}}, error}) => {
            localStorage.setItem('user', JSON.stringify(user));
            location.href = 'cartera';
        }).fail(response => {
            if (response.responseJSON) {
                const {status, code, response: {message}} = response.responseJSON;
                switch (true) {
                    case code >= 500:
                        toastr.error('An error ocurred.');
                        console.error(error_message, response.responseJSON);
                        break;
                    case code >= 400:
                        toastr.warning(message);
                        console.warn(message);
                        return;
                    default:
                        toastr.error('An error ocurred.');
                        console.error(response.responseJSON);
                        break;
                }
            } else if (response.responseText) {
                toastr.error('An error ocurred.');
                console.error(`${response.responseText}`);
            }
        });

        return false;
    });
});
