'use strict';

var Export = {

    form: '#tb-export-form',

    download: function (model) {
        var values = $(Export.form).serializeArray();
        var out = new Array();

        values.push({ name: 'model', value: model });

        $.each(values, function (index, val) {
            out.push(val['name'] + '=' + val['value']);
        });

        var url = TableBuilder.options.action_url + '/export?' + out.join('&');

        location.href = url;
    }
};
