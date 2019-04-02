<label class="select">
    <select {{ request()->has('id') && $readonly_for_edit ? 'disabled' : '' }} name="{{ $name }}"
            class="dblclick-edit-input form-control input-small unselectable {{ $action ? 'action' : '' }}">
        @foreach ($options as $value => $caption)
            <option value="{{ $value }}" {{ $value == $selected ? 'selected' : '' }} selected>{{ __cms($caption) }}</option>
        @endforeach
    </select>
    <i></i>
</label>

@if($comment)
  <div class="note">
      {{ $comment }}
  </div>
@endif
