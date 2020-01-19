<style>
    .filter_autocomplete .select2-container .select2-choice .select2-arrow{
        display: none;
    }
    .filter_autocomplete .select2-container .select2-choice{
        cursor: text
    }
</style>

<div class="filter_autocomplete" style="position: relative; min-width: 125px">
    <input class="select2-enabled filter_{{ $field->getNameField() }}" type="hidden" id="filter[{{ $field->getNameField() }}]" name="filter[{{$field->getNameField()}}]" style="width:100%;" value="{{$filterValue}}">
    @if ($filterValue)
        <button onclick="$(this).parent().find('input').val(''); setTimeout(function(){ TableBuilder.search(); }, 200); return false;" class="close" style="position: absolute; top: 8px; right: 6px;">
            ×
        </button>
    @endif
</div>

<script>
    $(document).ready(function() {
        var filter_{{ $field->getNameField() }} =  $('.filter_{{ $field->getNameField() }}').select2({
            minimumInputLength: 3,
            multiple: false,
            language: "ru",
            ajax: {
                url: '/admin/actions/{{request()->segment(count(request()->segments()))}}',
                dataType: 'json',
                type: 'POST',
                quietMillis: 200,
                data: function (term, page) {
                    return {
                        q: term,
                        limit: 20,
                        ident: '{!! $field->getNameField() !!}',
                        query_type: 'foreign_ajax_search',
                    };
                },
                results: function (data, page) {
                    return data;
                }
            },
            formatResult: function (item) {
                return item.name;
            },
            formatSelection: function (item) {
                return item.name;
            },
            formatNoMatches: function () {
                return 'Ничего не найдено';
            },
            formatSearching: function () {
                return "Ищет...";
            },
            formatInputTooShort: function (input, min) {
                var n = min - input.length;
                return "Введите текст";
            },

            dropdownCssClass: "bigdrop",

            escapeMarkup: function (m) {
                return m;
            }
        });

        @if ($filterValue)
        filter_{{ $field->getNameField() }}.select2("data", {'id' : '{{$filterValue}}', 'name': '{{$field->getValueForFilter($definition, $filterValue)}}'});
        @endif

    });
</script>
