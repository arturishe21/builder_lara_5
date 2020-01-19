<?php
    $selected = $field->getOptionsSelected($definition);
?>

<section class="section_field">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <select class="select2-enabled" multiple style="width:100%" name="{{$field->getNameField()}}[]" id="{{$field->getNameField()}}">
                        @foreach ($field->getOptions($definition) as $key => $title)
                            <option value="{{$key}}" {{in_array($key, $selected) ? 'selected' : ''}}>
                                {{ trim($title) }}
                            </option>
                        @endforeach

                </select>

                <script>
                    jQuery(document).ready(function() {
                        jQuery("#{{$field->getNameField()}}").select2();
                    });
                </script>
            </div>
        </div>
    </div>
</section>
