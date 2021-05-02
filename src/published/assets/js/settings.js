"use strict";

var Settings =
    {
        init: function()
        {
            Settings.handleStartLoad();

            $(".select_text").click(function(){
                var w = window.getSelection();
                var range = document.createRange();
                range.selectNode(this);
                w.addRange(range);
            });

            $('.selectable').editable();

        }, // end init

        handleSaveSetting: function()
        {
            var langEditor = "en";

            langEditor =  langCms == "ua" ? 'uk' : langCms;

            var csrfToken = $("meta[name=csrf-token]").attr("content");

            var option =  {
                inlineMode: false,
                imageUploadURL: '/admin/upload_image?_token=' + csrfToken,
                imageManagerDeleteURL: "/admin/delete_image?_token=" + csrfToken,
                heightMin: 100,
                heightMax: 500,
                fileUploadURL: "/admin/upload_file?_token=" + csrfToken,
                imageManagerLoadURL: "/admin/load_image?_token=" + csrfToken,
                imageDeleteURL: "/admin/delete_image?_token=" + csrfToken,
                language: langEditor,
                imageEditButtons: ['imageReplace', 'imageAlign', 'imageRemove', '|', 'imageLink', 'linkOpen', 'linkEdit', 'linkRemove', '-', 'imageDisplay', 'imageStyle', 'imageAlt', 'imageSize', 'crop'],
            };


            $('.text_block').froalaEditor(option);

            //$("a[href='https://froala.com/wysiwyg-editor']").parent().remove();

            var $checkoutForm = $('#form_page').validate({
                rules : {
                    title : {
                        required : true
                    },
                    slug : {
                        required : true
                    }
                },
                messages : {
                    title : {
                        required : 'Нужно заполнить название'
                    },
                    slug : {
                        required : 'Нужно заполнить код '
                    }
                },
                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                },
                submitHandler: function(form) {

                    Settings.doSaveSetting();
                }
            });
        }, //handleSaveSetting

        handleStartLoad: function()
        {
            var idPage = Core.urlParam('id_setting');
            if ($.isNumeric(idPage)) {
                Settings.getEdit(idPage);
            }

            var activeGroup = $.trim($(".btn-group ul li.active").text());
            $(".btn-group button span").text(activeGroup);

        }, // end handleStartLoad

        getCreateForm: function()
        {
            $("#modal_form").modal('show');
            Settings.preloadPage();

            $.post("/admin/settings/create", {},
                function(data) {
                    $("#modal_form .modal-content").html(data);
                    Settings.handleSaveSetting();
                });
        }, // end getCreateForm

        doSaveSetting: function()
        {

            Settings.loadAction();

            console.log($('#form_page').serializeArray());

            var fileData = $("#modal_form [name=file]").prop("files")[0];
            var formData = new FormData();
            var id = $('#form_page [name=id]').val();
            formData.append("file", fileData)
            formData.append("data", $('#form_page').serialize());

            var url = id ? '/admin/settings/' + id + '/update' : '/admin/settings/store';

            $.ajax({
                url: url,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function(response) {

                    Settings.loadAction("hide");

                    if (response.status == "ok") {
                        TableBuilder.showSuccessNotification(response.ok_messages);
                        $("#modal_form").modal('hide');
                        $(".modal-backdrop").remove();

                        doAjaxLoadContent(window.location.href);
                    } else {
                        var messErrors = ""
                        $.each( response.errors_messages, function( key, value ) {
                            messErrors += value + "<br>";
                        });

                        TableBuilder.showErrorNotification(messErrors);
                    }
                }
            });
        }, // end doSaveSetting

        activeToggle : function(idPage, checked)
        {
            var value = checked ? 1 : 0;

            $.post("/admin/settings/fast-save/" + idPage, {value : value },
                function(data){

                });
        },

        getEdit: function(idPage)
        {
            $("#modal_form").modal('show');
            Settings.preloadPage();

            $("#modal_form .modal-dialog").css("margin-top", $(document).scrollTop()+30);

            var groupParam = Core.urlParam("group");
            if (groupParam != null) {
                var urlPage = "?group=" + groupParam + "&id_setting=" + idPage;
            } else {
                var urlPage = "?id_setting=" + idPage;
            }

            window.history.pushState(urlPage, '', urlPage);

            $.post("/admin/settings/" + idPage +  "/edit", {},
                function(data) {
                    $("#modal_form .modal-content").html(data);

                    $('#form_page table.sort_table tbody').sortable(
                        {
                            items: "> tr" ,
                            handle: ".td_mov"
                        }
                    );
                    Settings.handleSaveSetting();
                }).fail(function(xhr) {
                var errorResult = jQuery.parseJSON(xhr.responseText);

                TableBuilder.showErrorNotification(errorResult.message);
                TableBuilder.hidePreloader();
            });
        }, // end getEdit

        preloadPage: function()
        {
            var preloadHtml = '<div id="table-preloader" class="text-align-center"><i class="fa fa-gear fa-4x fa-spin"></i></div>';
            $("#modal_form .modal-content").html(preloadHtml);

        }, // end preloadPage

        loadAction: function(action)
        {
            if (action=="hide") {
                $(".modal-footer .fa-spin").hide();
            } else {
                $(".modal-footer .fa-spin").show();
            }
        }, // end loadAction


        typeChange: function(type)
        {
            $(".types").hide();
            $(".type_" + type.value).show();
        }, // end typeChange

    };

jQuery(document).ready(function() {
    Settings.init();

    $(document).on('click', '#modal_form .close, .modal-footer button', function (e) {
        var url = Core.delPrm("id_setting");
        window.history.pushState(url, '', url);
    });
});


