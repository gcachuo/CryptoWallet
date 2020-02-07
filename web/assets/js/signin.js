$(function () {
    $("form").submit(function(e){
        e.preventDefault();
        toastr.info('true');

        return false;
    });
});
