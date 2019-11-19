
@if ($allow_foreign_add)
<script>
    $(document).ready(function () {
        $('label[for="{{ $name }}"]').append(' | <a href="" class="{{ $name }}_new_toggle fa fa-plus"></a>');
        $('.{{ $name }}_new_toggle').click(function(e) {
            e.preventDefault();
            var new_f_selector = $('.{{ $name }}_add_new_foreign');
            new_f_selector.slideToggle("fast", function () {
                if (!new_f_selector.is(":visible")) {
                    $('input[name={{ $name }}_new]').val('');
                }
            });
            $('.{{ $name }}_foreign').slideToggle("fast");
        });
    });
</script>
@endif

<label class="select">
    <select
     {{request("id") && $readonly_for_edit ? 'disabled' : ''}}

     name="{{ $name }}" class="dblclick-edit-input form-control input-small unselectable {{ $name }}_foreign">
        @if ($is_null)
            <option value="">{{ $null_caption ? : '...' }}</option>
        @endif

        @if ($recursive)
             @foreach ($options as $value => $caption)
                  {!! $caption !!}
             @endforeach
        @else
             @foreach ($options as $value => $caption)
                   <option value="{{ $value }}" {{$value == $selected
                    || ($value == request('foreign_field_id') && $name == request('foreign_field'))? "selected" : ""}} >{{ __cms($caption) }}</option>
             @endforeach
        @endif

    </select>
    <i></i>
</label>

@if ($allow_foreign_add)
    <div class="{{ $name }}_add_new_foreign" style="display: none">
        <input type="text" name="{{ $name }}_new_foreign" placeholder="{{__cms('Добавить новое значение')}}" />
    </div>
@endif

@if ($relation)
    <script>
        function change_relation_field{{$name}}() {
            $.post("/admin/change-relation-field", {id : $('[name={{$relation['field']}}]').val(), dataFieldJson : '{{json_encode($field)}}', selected : '{{$selected}}' },
                    function(data){
                        $('[name={{$name}}]').html(data);
                    });
        }

        change_relation_field{{$name}}();

        $('[name={{$relation['field']}}]').change(function () {
            change_relation_field{{$name}}();
        });

    </script>
@endif
