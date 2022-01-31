<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <label class="select">
                    <select
                        {{request("id") && $field->getReadonlyForEdit() ? 'disabled' : ''}}
                        @if ($field->isSaveOnChange())
                            onchange="TableBuilder.doSaveOnChange($(this), '{{request('id')}}')"
                        @endif

                        name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign">
                        @if ($field->isNullAble())
                            <option value="">{{ $field->getNullValue() ?: '...' }}</option>
                        @endif

                        @foreach ($field->getOptions($definition) as $value => $caption)
                            <option value="{{ $value }}" {{$value == $field->getValue()? "selected" : ""}} >{{ $caption }}</option>
                        @endforeach

                    </select>
                    <i></i>
                </label>

            </div>
        </div>
    </div>
</section>
