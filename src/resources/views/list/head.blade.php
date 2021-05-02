<tr>
    @if ($list->isSortable())
        <th style="width: 1%; padding: 0;">
            <i style="margin-left: -10px;" class="fa fa-reorder"></i>
        </th>
    @endif

    @if ($list->isMultiActions())
        <th style="width: 1%; padding: 0;">
            <label class="checkbox multi-checkbox multi-main-checkbox" onclick="TableBuilder.doSelectAllMultiCheckboxes(this);">
                <input type="checkbox" /><i></i>
            </label>
        </th>
    @endif

    @foreach ($list->head() as $field)
        @if ($field->isSortable())
            <th
                style="position: relative"
                class="sorting
                {!! $field->isOrder($list) !!}
                    "
                onclick="TableBuilder.doChangeSortingDirection('{{$field->getNameField()}}', this);"
            >
                @if ($field->isOrder($list))
                    <button onclick="TableBuilder.doClearOrder(); return false;" class="close" style="position: absolute; top: 12px; left: 13px;">×</button>
                @endif

                {{ $field->getName() }}
            </th>
        @else
            <th>{{ $field->getName() }}</th>
        @endif
    @endforeach

    @if ($list->isShowInsert())
        <th class="e-insert_button-cell" style="min-width: 69px;">
            {!! $list->actions()->fetch('insert') !!}
        </th>
    @else
        <th></th>
    @endif
</tr>
@if ($list->isFilterable())
    <tr class="filters-row">
        @if ($list->isSortable())
            <th></th>
        @endif

        @if ($list->isMultiActions())
            <th></th>
        @endif

        @foreach ($list->head() as $field)
            <td>{!! $field->getFilterInput($list) !!}</td>
        @endforeach

        <td style="width:1%">
            <button class="btn btn-default btn-sm tb-search-btn" style="min-width: 66px;"
                    type="button"
                    onclick="TableBuilder.search();">
                {{ __cms('Поиск')}}
            </button>
        </td>
    </tr>
@endif
