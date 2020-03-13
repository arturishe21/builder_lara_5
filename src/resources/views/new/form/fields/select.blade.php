<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="select">
                    <select
                        name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{$field->getAction() ? "action" : ""}}">
                        @foreach ($field->getOptions() as $value => $caption)
                                <option value="{{ $value }}"
                                    {{$value == $field->getValue() ? 'selected' : ''}}
                                >{{ __cms($caption) }}</option>
                        @endforeach
                    </select>
                    <i></i>
                </label>

                @if ($field->getComment())
                    <div class="note">
                        {{$field->getComment()}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
