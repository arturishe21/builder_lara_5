<section class="section_field">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <label class="select">
                    <select
                        {{request("id") && $field->isReadonlyForEdit() ? 'disabled' : ''}}

                        name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{ $field->getNameField() }}_foreign">
                        @if ($field->isNullAble())
                            <option value="">{{ $field->getNullValue() ?: '...' }}</option>
                        @endif

                        @foreach ($field->getOptions($definition) as $value => $caption)
                            <option value="{{ $value }}" {{$value == $field->getValue()? "selected" : ""}} >{{ __cms($caption) }}</option>
                        @endforeach

                    </select>
                    <i></i>
                </label>

            </div>
        </div>
    </div>
</section>
