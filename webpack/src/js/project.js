const Project = {
    init: function () {
        $("form").on('submit', event => {
            const $this = $(event.currentTarget);
            event.preventDefault();
            Project.request($this.data('action'), $this.serializeArray(), 'POST').done(data => {
                if (typeof data.response === 'string') {
                    toastr.info(data.response);
                }
                Project.navigate($this.data('redirect'), data);
            });
        });
    },
    navigate: function (file, data) {
        return $.get('pages/' + file + ".html", function (template) {
            const rendered = Mustache.render(template, data || {});
            $(".app").html(rendered);
            Project.init();
            Project.slideout.close();
            clearInterval(Project.refreshInterval);
        }, 'html');
    },
    request: function (uri, data, method) {
        return $.ajax(Project.url + uri, {
            method: method || 'GET',
            dataType: 'json',
            data: data,
            error: response => {
                if (response.responseJSON) {
                    const {status, code, response: {message}, error} = response.responseJSON;
                    switch (true) {
                        case code >= 500:
                            toastr.error('An error ocurred.');
                            console.error(response.responseJSON);
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
                    console.error(`${Project.url} ${response.responseText}`);
                }
            }
        });
    }
};
module.exports = Project;
