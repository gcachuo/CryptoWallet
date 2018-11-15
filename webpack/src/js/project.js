const Project = {
    init: function () {
        $("form").on('submit', event => {
            const $this = $(event.currentTarget);
            event.preventDefault();
            Project.request($this.attr('action'));
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
        return $.ajax(Project.url+uri, {
            method: method || 'GET',
            dataType: 'json',
            data: data,
            error: response => {
                if (response.responseText) {
                    console.error(`${Project.url} ${response.responseText}`);
                }
                alert('An error ocurred.');
            }
        });
    }
};
module.exports = Project;