
var Trans = {

    show_list: function(page,  count_show){
        doAjaxLoadContent(window.location.href);
        $(".modal-backdrop").remove();
    },

    getCreateForm: function(){
        $("#modal_form").modal('show');
        Trans.preloadPage();
        $.post("/admin/translations/create_pop", {},
            function (data) {
                $("#modal_form .modal-content").html(data);
            });
    },

    getTranslate: function(phrase)
    {

        $( ".langs_input" ).each(function( index ) {
            var language = $(this).attr("data-lang");
            if (phrase && language) {
                $(this).attr("placeholder","Переводит...");

                $.post("/admin/translations_cms/translate", {
                        phrase: phrase,
                        language: language
                    },
                    function (data) {
                          $("#langs_input_" + language).val(data.text);
                    }, "json");
            }
        });
    },

    AddRec : function()
    {
        var data = $('#form_page').serializeArray();

        console.log(data);

        $.post("/admin/translations/add_record", data,
            function (data) {
                if (data.status == "success") {

                    TableBuilder.showSuccessNotification(data.message);

                    $("#modal_form").modal('hide');
                    Trans.show_list(1);

                } else {
                    var errors = ""
                    $.each( data.message, function( key, value ) {
                        errors += value+"<br>";
                    });

                    TableBuilder.showErrorNotification(errors);
                }
            },"json");
    },

    doDelete : function(this_id_pages)
    {
        $.post("/admin/translations/del_record", {id : this_id_pages },
            function(data){
                Trans.show_list(1);
            });
    },

    preloadPage : function()
    {
        $("#modal_form .modal-content").html('<div id="table-preloader" class="text-align-center"><i class="fa fa-gear fa-4x fa-spin"></i></div>');
    },

    load_ajax : function($show)
    {
        if ($show == "show") {
            $(".load_ajax").show();
        } else {
            $(".load_ajax").hide();
        }
    },

    loadEditable : function()
    {
        $('.lang_change').editable2({
            url: '/admin/translations/change_text_lang',
            type: 'text',
            pk: 1,
            id: "",
            name: 'username',
            title: 'Enter username'
        });
    },

    createJsFile : function () {
        $.post("/admin/translations/create_js_file", {},
            function(data){

            });
    },
};

$(document).on("change", '[name=dt_basic_length]', function(){
    Trans.show_list("1", $(this).val());
});

$(document).on("keyup", '[name=search_q]', function(){
    var search_q = $("[type=search]").val();

    if (search_q.length > 1) {
        $(".load_page").show();
        $.post( window.location.pathname, {search_q : search_q, page : 1 })
            .done(function( data ) {
                $(".result_table").html(data);
                $(".load_page").hide();
            }).fail(function(xhr, ajaxOptions, thrownError) {
            var errorResult = jQuery.parseJSON(xhr.responseText);
            TableBuilder.showErrorNotification(errorResult.message);
        });
    }
});

$(document).ready(function(){
    Trans.loadEditable();
});
