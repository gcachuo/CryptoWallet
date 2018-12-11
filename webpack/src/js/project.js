const Project = {
    init: function () {
        Project.Users.loggedUser();
        Project.host = localStorage.getItem('host') || 'http://gcachuo.ml/cryptowallet/';
        Project.url = Project.host + 'api/';
        Project.slideout = new Slideout({
            'panel': $("#panel").get(0),
            'menu': $("#menu").get(0),
            'padding': 256,
            'tolerance': 70
        });
        $("form").on('submit', event => {
            const $this = $(event.currentTarget);
            event.preventDefault();
            Project.request($this.data('action'), $this.serializeArray(), 'POST').done(data => {
                if (typeof data.response === 'string') {
                    alert(data.response);
                }
                Project.navigate($this.data('redirect'), data);
            });
        });
        $(".toggle-button").on('click', function () {
            Project.slideout.toggle()
        });
        Project.slideout.close();
        clearInterval(Project.refreshInterval);
    },
    navigate: function (file, data) {
        return $.get('pages/' + file + ".html", function (template) {
            const rendered = Mustache.render(template, data || {});
            $(".app").html(rendered);
            Project.setCookie('page', file, 1);
            Project.init();
        }, 'html');
    },
    request: function (uri, data, method) {
        return $.ajax(Project.url + uri, {
            method: method || 'GET',
            dataType: 'json',
            data: data,
            error: response => {
                if (response.responseJSON) {
                    const result = response.responseJSON;
                    switch (result.code) {
                        case 500:
                            console.error(result.error.message);
                            break;
                        case 400:
                            alert(result.error.message);
                            return;
                        default:
                            console.error(result);
                            break;
                    }
                } else if (response.responseText) {
                    console.error(`${Project.url} ${response.responseText}`);
                }
                alert('An error ocurred.');
            }
        });
    },
    setCookie: function (name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    },
    getCookie: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
};
module.exports = Project;