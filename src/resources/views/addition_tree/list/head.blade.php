<tr>
    <th style="width: 1%; padding: 0;">
        <i style="margin-left: -10px;" class="fa fa-reorder"></i>
    </th>

    @foreach ($list->head() as $field)
         <th style="text-align: left">{{ $field->getName() }}</th>
    @endforeach

    @if ($list->isShowInsert())
        <th class="e-insert_button-cell" style="min-width: 69px;">
            {!! $list->actions()->fetch('insert') !!}
        </th>
    @else
        <th></th>
    @endif
</tr>
@if($current->parent_id)
    <tr>
        <td colspan="{{count($list->head()) + 2}}" style="text-align: left">
            <a href="?node={{$current->parent_id}}" class="node_link">&larr; {{__cms('Назад')}}</a>
        </td>
    </tr>
@endif
