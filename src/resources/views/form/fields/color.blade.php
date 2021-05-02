<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="input">
                    <input id="{{$field->getNameField()}}"
                           name="{{$field->getNameField()}}"
                           value="{{ $field->getValue() }}"
                           type="text"
                           class="form-control input-sm unselectable">

                    @if ($field->getComment())
                        <div class="note">
                            {{$field->getComment()}}
                        </div>
                    @endif
                </label>
                <script>
                    jQuery(document).ready(function() {
                        $('#{{$field->getNameField()}}').colorpicker().on('changeColor.colorpicker', function(event){
                            $("#{{$field->getNameField()}}").val(event.color.toHex());
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</section>


