<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <div class="no_active_froala">
                 <textarea class="text_block" name="{{ $field->getNameField()}}"
                            toolbar = "{{$field->getToolbar()}}"
                            inlineStyles = ''
                            options = '{{ $field->getOptions()}}'>{{ $field->getValue()  }}</textarea>
                </div>
                @if ($field->getComment())
                    <div class="note">
                        {{$field->getComment()}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
