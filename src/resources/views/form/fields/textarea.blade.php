<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="textarea">
                    <textarea rows="{{$rows ?? '3'}}"
                                                  class="custom-scroll"
                                                  id="{{ $field->getNameField() }}"

                                                  @if ($field->isDisabled())
                                                  disabled="disabled"
                                                  @endif

                                                  name="{{ $field->getNameField() }}">{{ $field->getValue() }}</textarea>
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
