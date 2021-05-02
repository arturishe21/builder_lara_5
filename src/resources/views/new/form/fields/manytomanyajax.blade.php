<?php
$selected = $field->getOptionsSelected($definition);
?>

<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <input class="select2-enabled" type="hidden" id="{{$field->getNameField()}}" name="{{$field->getNameField()}}" style="width:100%;">
            </div>
        </div>
    </div>
</section>
<script>

    jQuery(document).ready(function() {

        $select2{{$field->getNameField()}} = jQuery('#{{$field->getNameField()}}').select2({
            placeholder: "{{ $search['placeholder'] ?? 'Поиск' }}",
            minimumInputLength: {{ $search['minimum_length'] ?? '3' }},
            multiple: true,
            language: "ru",
            ajax: {
                url: jQuery('#{{$field->getNameField()}}').parents('form').attr('action'),
                dataType: 'json',
                type: 'POST',
                quietMillis: {{ $search['quiet_millis'] ?? '350' }},
                data: function (term, page) { // page is the one-based page number tracked by Select2
                    return {
                        q: term, //search term
                        limit: {{ $search['per_page'] ?? '20' }}, // page size
                        page: page, // page number
                        @if (isset($row['id']))
                        page_id : {{$row['id']}},
                        @endif
                        ident: '{!! $field->getNameField() !!}',
                        query_type: 'many_to_many_ajax_search',
                        paramsJson : '{ "model_parent" : "{{addslashes(addslashes($definition->getFullPathDefinition()))}}"}'
                    };
                },
                results: function (data, page) {
                    return data;
                }
            },
            formatResult: function(item) {
                return item.name;
            },
            formatSelection: function(item) {
                return item.name + '<span class="item_id" data-id="' + item.id + '"></span>';
            },
            formatNoMatches : function () {
                return 'По результату поиска ничего не найдено';
            },
            formatSearching: function () { return "Ищет..."; },
            formatInputTooShort: function (input, min) { var n = min - input.length; return "Введите еще " + n + "   символ "; },

            dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
            escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
        });

        @if ($selected)
            $select2{{$field->getNameField()}}.select2("data", {!! $selected !!});
        @endif
    });

    $('.select2-choices').sortable(
        {
            items: "> li.select2-search-choice",
            update: function (event, ui) {

                var ids = $(this).parent().find('.item_id');
                var arrIds = [];
                ids.each(function(i, elem) {
                    arrIds.push($(this).attr('data-id'));
                });

                $('[name={{$field->getNameField()}}]').val(arrIds.join(','));
            }
        }
    );
</script>
