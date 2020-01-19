<div style='clear:both; padding-top:10px;'></div>
<span>{{__cms('Показывать по')}}:</span>
<div class="btn-group">
    @foreach ($list->getPerPage() as $count)
        <button type="button"
                onclick="TableBuilder.setPerPageAmount('{{$count}}');"
                class="btn btn-default btn-xs {{$list->getDefinition()->getPerPageThis() == $count ? 'active' : ''}}">
            {{__cms($count)}}
        </button>
    @endforeach
</div>
