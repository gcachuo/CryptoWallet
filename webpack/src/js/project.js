const Project = {
    init: function () {
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
    },
    navigate: function (file, data) {
        return $.get('pages/' + file + ".html", function (template) {
            const rendered = Mustache.render(template, data || {});
            $(".app").html(rendered);
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
    }
};
module.exports = Project;