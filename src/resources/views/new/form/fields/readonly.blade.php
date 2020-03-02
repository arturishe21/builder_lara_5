<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                {{$field->getValue()}}
                <input
                    name="{{ $field->getNameField() }}"
                    type="hidden"
                    value="{{$field->getValue()}}"
                />
            </div>
        </div>
    </div>
</section>


