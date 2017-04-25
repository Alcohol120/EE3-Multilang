jQuery(document).ready(function () {

    var module = $("#multilang");

    module.find("#add_lang").click(function (e) {
        e.preventDefault();
        var table = module.find("#multilang_languages").find("tbody");
        table.find(".blank").clone().removeClass("blank").appendTo("#multilang_languages tbody");
    });

    $(document).on("click", "#multilang .remove_lang", function (e) {
        e.preventDefault();
        $(this).parents("tr").remove();
    });

});