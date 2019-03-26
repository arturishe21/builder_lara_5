<tr id-row="{{ $row['id'] }}" id="sort-{{ $row['id'] }}">
    @if($def->isSortable())
        <td class="tb-sort" style="cursor:s-resize;">
            <i class="fa fa-sort"></i>
        </td>
    @endif

    @if($def->hasMultiActions())
        <td>
            <label class="checkbox multi-checkbox">
                <input type="checkbox" value="{{$row['id']}}" name="multi_ids[]" /><i></i>
            </label>
        </td>
    @endif

@foreach($def->getFields() as $field)
    @php $field = $controller->getField($field->getFieldName()) @endphp
    @if(!$field->getAttribute('hide_list'))
        <td width="{{ $field->getAttribute('width') }}" class="{{ $field->getAttribute('class') }} unselectable">
            @if($field->getAttribute('fast-edit'))
                {!! $field->getListValueFastEdit($row, $ident) !!}
            @elseif($field->getAttribute('result_show'))
                {!! strip_tags($field->getReplaceStr($row), "<a><span><img><br>") !!}
            @else
                <span>{!! strip_tags($field->getListValue($row), "<a><span><img><br>") !!}</span>
            @endif
        </td>
    @endif
@endforeach
    {!! $controller->view->fetchActions($row) !!}
</tr>
