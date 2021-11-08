
<article class="{{$modelCard->size}}" id="article_chart_{{$k}}" style="margin-bottom: 20px">
    <div class="jarviswidget" data-widget-colorbutton="false" data-widget-sortable="false">
        <header style="padding-right: 5px; height: 41px">
            <span class="widget-icon"> <i class="far fa-chart-bar"></i></span>
            <h2>{{$modelCard->title}}</h2>
            <div style="text-align: right">
                <label class="input" style="width: 110px; position: relative">
                    <input type="text"
                           autocomplete="off"
                           class="form-control datepicker_trend"
                           name="trend_from"
                           value="{{$modelCard->currentRange()[0]}}"
                    >
                    <i class="fa fa-calendar" style="position: absolute; top: 10px; right: 10px"></i>
                </label>
                <label class="input" style="width: 110px; position: relative">
                    <input type="text"
                           autocomplete="off"
                           class="form-control datepicker_trend"
                           name="trend_to"
                           value="{{$modelCard->currentRange()[1]}}"
                    >

                        <i class="fa fa-calendar" style="position: absolute; top: 10px; right: 10px"></i>

                </label>

                <input type="hidden" value="{{get_class($modelCard)}}" name="trend_model">
            </div>
        </header>
        <!-- widget div-->
        <div>
            <!-- widget content -->
            <div class="widget-body padding">
                <div style="height: 300px" class="trends">
                <canvas id="chart_trend{{$k}}"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- end widget -->
</article>
