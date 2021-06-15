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

                        @if ($field->getValue() && $field->getReadonlyForEdit())
                            readonly
                        @endif

                        type="text"
                        value="{{ $field->getValue() }}"
                        name="{{ $field->getNameField() }}"
                        placeholder="{{ $field->getPlaceholder() }}"
                        class="dblclick-edit-input form-control input-sm unselectable"
                        data-name-input="{{$definition->getNameDefinition().$field->getNameField()}}"
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
@include('admin::form.fields.partials.traslation')

