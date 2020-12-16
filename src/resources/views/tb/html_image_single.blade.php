<div class="{{strpos($value, ".svg") || strpos($value, ".png") || strpos($value, ".gif") ? 'transparent-image' : ''}}" style="position: relative; display: inline-block;" >
    <img class="image-attr-editable"
         data-tbident="{{$name}}"

         @if ($width)
            data-width="{{$width}}"
            style="max-width: {{$width}}"
         @endif
         data-height="{{$height}}"
         @if (strpos($value, ".svg"))
            width="{{$width}}"
            src="/{{ $value}}" src_original="{{$value}}"
         @else
           src="{{ glide($value, ['w' => $width, 'h' => $height]) }}" src_original="{{$value}}"
         @endif
     />
    <div class="tb-btn-delete-wrap">
        <button class="btn btn-default btn-sm tb-btn-image-delete"
           type="button"
           onclick="TableBuilder.deleteSingleImage('{{$name}}', this);">
           <i class="fa fa-times"></i>
        </button>
    </div>
</div>