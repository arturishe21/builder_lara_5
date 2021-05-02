
<section id="widget-grid" class="">
    @include('admin::partials.cards')

    <div class="row" style="padding-right: 13px; padding-left: 13px;">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-right: 0px; padding-left: 0px;">
            <div id="table-preloader" class="smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
            <div class="jarviswidget jarviswidget-color-blue" id="wid-id-1"
                 data-widget-editbutton="false"
                 data-widget-colorbutton="false"
                 data-widget-deletebutton="false"
                 data-widget-sortable="false">
                {!!  $filterView ?? '' !!}
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>{{ $list->title() }}</h2>

                    @foreach($list->getDefinition()->buttons() as $button)
                        {!! (new \Vis\Builder\Services\ButtonStrategy(new $button($list)))->render() !!}
                    @endforeach
                </header>
                <div>
                    <div class="jarviswidget-editbox"></div>
                    <div class="widget-body no-padding">
                        <form
                            action="{{$list->getUrlAction()}}"
                            method="post"
                            class="form-horizontal tb-table"
                            target="submiter" >

                            <table id="datatable_fixed_column" class="table  table-hover table-bordered">
                                <thead>
                                @include('admin::list.head')
                                </thead>
                                <tbody>
                                @include('admin::list.body')
                                </tbody>
                                @include('admin::list.footer')
                            </table>
                            @include('admin::list.pagination')
                        </form>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>

<script>
    $(".breadcrumb").html("<li><a href='/admin'>{{__cms('Главная')}}</a></li> <li>{{ $list->title() }}</li>");
    $("title").text("{{ $list->title() }} - {{ __cms('Административная часть сайта') }}");

    TableBuilder.optionsInit({
        action_url: '{{ $list->getUrlAction() }}'
    });

    TableBuilder.action_url = '{{ $list->getUrlAction() }}';
</script>
