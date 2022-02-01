'use strict';

var QuickEdit  = {

    option :  {
        toolbarInline: true,
        alwaysVisible: true,
        imageUploadURL: '/admin/upload_image?_token=' + $("meta[name=csrf-token]").attr("content"),
        imageManagerDeleteURL: "/admin/delete_image?_token=" + $("meta[name=csrf-token]").attr("content"),
        heightMin: 100,
        fileUploadURL: "/admin/upload_file?_token=" + $("meta[name=csrf-token]").attr("content"),
        imageManagerLoadURL: "/admin/load_image?_token=" + $("meta[name=csrf-token]").attr("content"),
        imageDeleteURL: "/admin/delete_image?_token=" + $("meta[name=csrf-token]").attr("content"),
        language: 'ru',
        imageEditButtons: ['imageReplace', 'imageAlign', 'imageRemove', '|', 'imageLink', 'linkOpen', 'linkEdit', 'linkRemove', '-', 'imageDisplay', 'imageStyle', 'imageAlt', 'imageSize', 'crop'],
    },

    saveData : function (value, content) {
        $.ajax({
            method: "POST",
            url: "/admin/save_edit_on_site",
            data: {
                model: content.attr('data-model'),
                id:  content.attr('data-id'),
                field: content.attr('data-field-name'),
                language: content.attr('data-language'),
                value: value,
                _token: $("meta[name=csrf-token]").attr("content")
            }
        }).done(function( msg ) {});
    },

    delay : function (callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    },

    init : function () {

        $('.edit_for_admin_html').froalaEditor(QuickEdit.option);

        $('.edit_for_admin_html').on('froalaEditor.contentChanged', function (e, editor) {
            var textEditor = $(this).froalaEditor('html.get');
            QuickEdit.saveData(textEditor, $(this));
        });

        $('.edit_for_admin').keyup(QuickEdit.delay(function (e) {
            QuickEdit.saveData($(this).html(), $(this));
        }, 1000));

    }
};

QuickEdit.init();
