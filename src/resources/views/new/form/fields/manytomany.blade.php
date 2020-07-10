<?php
    $selected = $field->getOptionsSelected($definition);
?>

<section class="{{$field->getClassName()}}">
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

                <input type="hidden" name="{{$field->getNameField()}}">

                <script>
                    jQuery(document).ready(function() {
                        jQuery("#{{$field->getNameField()}}").select2();
                    });

                    $('.select2-choices').sortable(
                            {
                                items: "> li.select2-search-choice",
                                update: function (event, ui) {

                                    var ids = $(this).parent().find('.item_id');
                                    var arrIds = [];
                                    ids.each(function(i, elem) {
                                        arrIds.push($(this).attr('data-id'));
                                    });

                                    $('[name={{$field->getNameField()}}]').val(arrIds.join(','));
                                }
                            }
                    );

                </script>
            </div>
        </div>
    </div>
</section>
