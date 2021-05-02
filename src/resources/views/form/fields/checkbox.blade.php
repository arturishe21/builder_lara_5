<section class="{{$field->getClassName()}}">
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="checkbox">
                    <input type="checkbox"
                           id="{{$field->getNameField()}}"
                           name="{{ $field->getNameField() }}"
                           @if ($field->getValue())
                           checked="checked"
                           @endif

                           @if ($field->isDisabled())
                           disabled="disabled"
                           @endif

                           value = '1'
                    >
                    <i></i>{{$field->getName()}}</label>
            </div>
        </div>
    </div>
</section>
