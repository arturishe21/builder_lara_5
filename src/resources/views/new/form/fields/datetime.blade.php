<section class="section_field">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="input">
                    <input type="text"
                           id="{{ $field->getNameField() }}"
                           value="{{$field->getValue()}}"
                           name="{{$field->getNameField()}}"

                           @if ($field->isDisabled())
                           disabled="disabled"
                           @endif

                           autocomplete="off"

                           class="form-control datepicker" >

                    <span class="input-group-addon form-input-icon">
                        <i class="fa fa-calendar"></i>
                    </span>
                </label>

                @if ($field->getComment())
                    <div class="note">
                        {{$field->getComment()}}
                    </div>
                @endif
                <script>
                    jQuery(document).ready(function() {
                        jQuery("#{{ $field->getNameField()}}").datetimepicker({
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: {{ $months ?? '1' }},
                            prevText: '<i class="fa fa-chevron-left"></i>',
                            nextText: '<i class="fa fa-chevron-right"></i>',
                            dateFormat: "yy-mm-dd",
                            timeFormat: 'HH:mm:ss',
                            //showButtonPanel: true,
                            regional: ["ru"],
                            onClose: function (selectedDate) {}
                        });
                    });
                </script>


            </div>
        </div>
    </div>
</section>


