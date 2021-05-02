<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">
                <label class="select">
                    <select
                        name="{{ $field->getNameField() }}" class="dblclick-edit-input form-control input-small unselectable {{$field->getAction() ? "action" : ""}}">
                        @foreach ($field->getOptions() as $value => $caption)
                                <option value="{{ $value }}"
                                    {{$value == $field->getValue() ? 'selected' : ''}}
                                >{{ $caption }}</option>
                        @endforeach
                    </select>
                    <i></i>
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

@if ($field->getActionSelect())

    <script>
        $('select[name={{ $field->getNameField() }}]').change(function () {

            if (!$(this).val()) {
                $('select[name={{ $field->getActionSelect() }}] option').show();
                return;
            }

            $('select[name={{ $field->getActionSelect() }}] option').hide();
            $('select[name={{ $field->getActionSelect() }}] option[data-class=' + $(this).val() + ']').show();
            $('select[name={{ $field->getActionSelect() }}] option[value=""').show();
            $('select[name={{ $field->getActionSelect() }}]').prop("selected", true).val('').change();

        });

        if ($('select[name={{ $field->getNameField() }}]').val()) {
            $('select[name={{ $field->getActionSelect() }}] option').hide();
            $('select[name={{ $field->getActionSelect() }}] option[data-class=' + $('select[name={{ $field->getNameField() }}]').val() + ']').show();
            $('select[name={{ $field->getActionSelect() }}] option[value=""').show();
        }

    </script>
@endif
