<td style="width: 80px">
    @if ($collectionActions)
    <div style="display: inline-block">
        <div class="btn-group  pull-right">
            <a class="btn dropdown-toggle btn-default"  data-toggle="dropdown">
                <i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu">
                @foreach($collectionActions as $actionType)
                    {!! $action->fetch($actionType, $record); !!}
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</td>

