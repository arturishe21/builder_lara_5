<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="select">
                    <select
                            name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{$field->getAction() ? "action" : ""}}">
                        @foreach ($field->getOptions() as $key => $arrayValues)
                            <option
                                    value="{{ $key }}"
                            {{$key == $field->getValue() ? 'selected' : ''}}

                            @foreach ($arrayValues as $dataKey => $dataValue)
                                {{$dataKey}}='{{$dataValue}}'
                            @endforeach
                            >{{ $arrayValues['value'] }}</option>
                        @endforeach
                    </select>
                    <i></i>
                </label>

                @if ($field->getComment())
                    <div class="note">
                        {{$field->getComment()}}
                    </div>
                @endif
                <div class="imgdisplay {{ $field->getNameField() }}" style="display: none; text-align: center; padding-top: 15px">
                    <img src="">
                </div>
            </div>
        </div>
    </div>
</section>

<script>

    $('.select').on('change', 'select[name={{ $field->getNameField() }}]', function (e) {
        showImg{{ $field->getNameField()}}(this.value);
    });

    function showImg{{ $field->getNameField()}}(id) {

        if (!id) {
            $('.{{ $field->getNameField()}}.imgdisplay').hide();
            return;
        }

        $('.{{ $field->getNameField()}}.imgdisplay').show();
        var img = $('select[name={{ $field->getNameField() }}] option[value=' + id + ']').attr('data-img');

        $('.{{ $field->getNameField()}}.imgdisplay img').attr('src', img);
    }

    showImg{{ $field->getNameField()}}($('select[name={{ $field->getNameField() }}]').val());

</script>
