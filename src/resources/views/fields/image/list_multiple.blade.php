<div style="cursor:pointer;height: 50px;overflow: hidden;" onclick="$(this).css('height', 'auto').css('overflow', 'auto');">
    @foreach($images as $src)
        <img height="{{ $this->getAttribute('img_height', '50px') }}"
             src="{{ $this->getAttribute('is_remote') ? $src : asset($src) }}" /><br>';
    @endforeach
</div>
