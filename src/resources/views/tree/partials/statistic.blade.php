@if ($active)
    <li><a onclick="TableBuilder.getViewsStatistic({{ $item->id }}, this);" ><i class="fa fa-bar-chart"></i> {{__cms($caption)}} </a></li>
@endif