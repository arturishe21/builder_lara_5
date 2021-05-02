<?php
    $selected = $field->getOptionsSelected($definition);
?>

<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content {{$field->getNameField()}}_select">

                <select class="select2-enabled" multiple style="width:100%" id="{{$field->getNameField()}}">
                        @foreach ($field->getOptions($definition) as $key => $title)
                            <option value="{{$key}}" {{in_array($key, $selected) ? 'selected' : ''}}>
                                {{ trim($title) }}
                            </option>
                        @endforeach
                </select>

                <input type="hidden" name="{{$field->getNameField()}}" value="{{implode(',', $selected)}}">

                <script>

                    $('#{{$field->getNameField()}}').change(function () {

                        var ids = $('.{{$field->getNameField()}}_select .select2-choices').find('.item_id');
                        var arrIds = [];
                        ids.each(function(i, elem) {
                            arrIds.push($(this).attr('data-id'));
                        });

                        $('[name={{$field->getNameField()}}]').val(arrIds.join(','));
                    });

                    jQuery(document).ready(function() {
                        jQuery("#{{$field->getNameField()}}").select2({
                            formatResult: function(item) {
                                return item.text;
                            },
                            formatSelection: function(item) {
                                return item.text + '<span class="item_id" data-id="' + item.id + '"></span>';
                            },
                            formatNoMatches : function () {
                                return 'По результату поиска ничего не найдено';
                            },
                            formatSearching: function () { return "Ищет..."; },
                            formatInputTooShort: function (input, min) { var n = min - input.length; return "Введите еще " + n + "   символ "; },

                            dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
                            escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results

                        });
                    });

                    $('.{{$field->getNameField()}}_select .select2-choices').sortable(
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
