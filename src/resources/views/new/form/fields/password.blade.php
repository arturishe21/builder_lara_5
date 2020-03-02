<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="input">
                    <input
                        @if ($field->isDisabled())
                        disabled="disabled"
                        @endif
                        type="password"
                        value="{{ $field->getValue() }}"
                        name="{{ $field->getNameField() }}"
                        placeholder="{{ $field->getPlaceholder() }}"
                        class="dblclick-edit-input form-control input-sm unselectable"
                    />

                    @if ($field->getComment())
                        <div class="note">
                            {{$field->getComment()}}
                        </div>
                    @endif
                </label>
            </div>
        </div>

    </div>
</section>


