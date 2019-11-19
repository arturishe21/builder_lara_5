<div class="div_input">
    <div class="input_content">
        <label class="input">
            <input

                    @if ($disabled)
                        disabled="disabled"
                    @endif

                    @if ($is_password)
                        type="password"
                        @if ((isset($value) && $value))
                            value="password"
                        @endif
                    @else
                        type="{{$custom_type ? $custom_type : 'text'}}"
                        value="{{ $value }}"
                    @endif
                    name="{{ $name }}"
                    placeholder="{{ $placeholder }}"
                    @if ($mask)
                        data-mask="{{$mask}}"
                    @endif
                    @if (request("id") && $readonly_for_edit)
                        disabled
                    @endif
                    class="dblclick-edit-input form-control input-sm unselectable {{$only_numeric ? "only_num" : ""}}"
            />

            @if ($custom_type == 'hidden')
                {{ $value }}
            @endif

            @if (isset($comment) && $comment)
                <div class="note">
                    {{$comment}}
                </div>
            @endif
        </label>
    </div>
</div>

<script>
    @if ($transliteration && isset($transliteration['field']))

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
