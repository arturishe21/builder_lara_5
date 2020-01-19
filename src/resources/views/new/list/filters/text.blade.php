
<div style="position: relative;">
    <input type="text" value="{{$filterValue}}" name="filter[{{ $field->getNameField() }}]" class="form-control input-small" />

        @if ($filterValue)
        <button onclick="$(this).parent().find('input').val(''); setTimeout(function(){ TableBuilder.search(); }, 200); return false;" class="close" style="position: absolute; top: 8px; right: 6px;">
            ×
        </button>
        @endif

</div>
