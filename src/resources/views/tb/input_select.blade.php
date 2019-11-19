<label class="select">
    <select

    @if (request("id") && $readonly_for_edit)
        disabled
    @endif

    name="{{ $name }}" class="dblclick-edit-input form-control input-small unselectable {{$action ? "action" : ""}}">
        @foreach ($options as $value => $caption)
            @if ($value == $selected)
                <option value="{{ $value }}" selected>{{ __cms($caption) }}</option>
            @else
                <option value="{{ $value }}">{{ __cms($caption) }}</option>
            @endif
        @endforeach
    </select>
    <i></i>
</label>
@if (isset($comment) && $comment)
  <div class="note">
      {{$comment}}
  </div>
@endif
