<section class="section_field">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="input">
                    <input
                        @if ($field->isDisabled())
                        disabled="disabled"
                        @endif

                        type="text"
                        value="{{ $field->getValue() }}"
                        name="{{ $field->getNameField() }}"
                        placeholder="{{ $field->getPlaceholder() }}"
                        class="dblclick-edit-input form-control input-sm unselectable"
                    />
                    @if ($field->getComment())
                        <div class="note">
                            {{$field->getComment()}}
                        </div>
                    @endif
                </label>
            </div>
        </div>

        <script>
                @if (isset($transliteration) && isset($transliteration['field']))

            var runTrans = true;
            @if (isset($transliteration['only_empty']) && $transliteration['only_empty'] == true)
                runTrans = $('[name={{$transliteration['field']}}]').val() == '' ? true : false;
            @endif

            if (runTrans) {
                $('[name={{$transliteration['field']}}]').keyup(function(){
                    $('[name={{ $name }}]').val(TableBuilder.urlRusLat($(this).val()));
                });
            }
            @endif
        </script>
    </div>
</section>


