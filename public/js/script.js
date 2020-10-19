$(document).ready(function () {
    $(".form-reset").click(function () {
        $("form").trigger('reset');
    });
    $(".form-save").click(function () {
        $("form").prepend("<input type='text' class='d-none' name='submit_btn' value='save'>");
        $("form").submit();
    });
    $(".form-save-back").click(function () {
        $("form").prepend("<input type='text' class='d-none' name='submit_btn' value='save_back'>");
        $("form").submit();
    });
});
