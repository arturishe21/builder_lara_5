<div style='clear:both; padding-top:10px;'></div>
<span>{{__cms('Показывать по')}}:</span>
<div class="btn-group">
    @foreach (config('builder.'.$treeName.'.pagination.per_page') as $amount => $caption)
        <button type="button"
                onclick="TableBuilder.setPerPageAmount('{{$amount}}');"
                class="btn btn-default btn-xs {{$perPage == $amount ? 'active' : ''}}">
            {{__cms($caption)}}
        </button>
    @endforeach
</div>
