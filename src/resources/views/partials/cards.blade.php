@if (count($list->getDefinition()->cards()))
    <div class="row">
        @foreach ($list->getDefinition()->cards() as $k => $model)
            <div id="card{{$k}}">
                <?php $modelCard = new $model(); ?>
                @if ($modelCard instanceof \Vis\Builder\Services\Value)
                    @include('admin::partials.card_value')
                @elseif ($modelCard instanceof \Vis\Builder\Services\Trend)
                    @include('admin::partials.card_trend')
                @endif
            </div>
        @endforeach
    </div>

@endif
