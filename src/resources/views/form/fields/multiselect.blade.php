<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="select-multiple" style="width: 100%">
                    <input type="hidden" name="{{ $field->getNameField() }}[]" value="">
                    <select multiple
                            size="5"
                            name="{{ $field->getNameField() }}[]"
                            class="dblclick-edit-input form-control input-small unselectable">
                    @foreach ($field->getOptions() as $value => $caption)
                    <option value="{{ $value }}"
                            {{in_array($value, $field->getValue()) ? 'selected' : ''}}
                        >{{ $caption }}</option>
                    @endforeach
                    </select>
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
