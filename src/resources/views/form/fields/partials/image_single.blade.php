@if ($field->getValue())
    <div class="{{$field->isTransparent() ? 'transparent-image' : ''}}" style="position: relative; display: inline-block;" >
        <img class="image-attr-editable"
             data-tbident="{{$field->getNameField()}}"
             @if (strpos($field->getValue(), ".svg"))
                width="200"
                src="{{ $field->getValue()}}" src_original="{{$field->getValue()}}"
             @else
                 src="{{ glide($field->getValue(), ['w' => 200, 'h' => 200]) }}"
                 src_original="{{$field->getValue()}}"
            @endif

            style="max-width: 200px"
        />
        <div class="tb-btn-delete-wrap">
            <button class="btn btn-default btn-sm tb-btn-image-delete"
                    type="button"
                    onclick="TableBuilder.deleteSingleImage('{{$field->getNameField()}}', this);">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
@else
    <p style="padding: 20px 0 10px 0">{{__cms('Нет изображения')}}</p>
@endif
