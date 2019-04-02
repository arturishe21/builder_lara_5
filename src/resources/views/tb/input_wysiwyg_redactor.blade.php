<div class="no_active_froala">
      <textarea class="text_block" name="{{ $name }}" toolbar="{{ $toolbar }}"
                inlineStyles='{{ $inlineStyles ? json_encode($inlineStyles) : ''}}'
                options='{{ $options ? json_encode($options) : ''}}'>{{ $value  }}</textarea>
</div>
@if($comment)
    <div class="note">
        {{ $comment }}
    </div>
@endif
