
<tr id-row="{{ $record->id }}" id="sort-{{ $record->id }}">
    @if ($list->isSortable())
        <td class="tb-sort" style="cursor:s-resize;">
            <i class="fa fa-sort"></i>
        </td>
    @endif

    @if ($list->isMultiActions())
        <td>
            <label class="checkbox multi-checkbox">
                <input type="checkbox" value="{{$record->id}}" name="multi_ids[]" /><i></i>
            </label>
        </td>
    @endif

    @foreach ($record->fields as $field)
        <td unselectable>
            <span>{!! $field->value !!}</span>
        </td>
    @endforeach

    {!! $list->actions()->list($record) !!}

</tr>
