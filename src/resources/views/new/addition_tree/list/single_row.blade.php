<tr id-row="{{ $record->id }}" id="sort-{{ $record->id }}" data-id="{{ $record->id }}">
    <td class="tb-sort-me-gently" style="cursor:s-resize;">
        <i class="fa fa-sort"></i>
    </td>
    @foreach ($record->fields as $ident => $field)
        <td unselectable style="text-align: left">
            @if ($ident == 'title')
                <i class="{{$record->isHasChildren() ? 'fa fa-folder' : 'fal fa-file'}}"></i>&nbsp;
                <a href="?node={{ $record->id }}" class="node_link">{{ $field->value }}</a>
            @else
                <span>{!! $field->value !!}</span>
            @endif

        </td>
    @endforeach
    {!! $list->actions()->list($record) !!}
</tr>
