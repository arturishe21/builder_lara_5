@section('title')
  {{ $def->getCaption() }}
@stop

@section('ribbon')
   <ol class="breadcrumb">
        <li><a href="/admin"> {{__cms('Главная')}}</a></li>
        @if($def->getCaption())
            <li>{{ $def->getCaption()  }}</li>
        @endif
   </ol>
@stop
<section id="widget-grid" class="">

    @include('admin::partials.cards')

    @if($def->getFields())
        <div class="row" style="padding-right: 13px; padding-left: 13px;">
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-right: 0px; padding-left: 0px;">
                <div id="table-preloader" class="smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
                <div class="jarviswidget jarviswidget-color-blue" id="wid-id-1"
                    data-widget-editbutton="false"
                    data-widget-colorbutton="false"
                    data-widget-deletebutton="false"
                    data-widget-sortable="false">
                    {!!  $filterView !!}
                    <header>
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>{{ $def->getCaption() }}</h2>
                        {!! $def->hasButtons() ?  $controller->buttons->fetch() : '' !!}
                        {!! $def->hasExportButtons()  ? $controller->import->fetch() : '' !!}
                        {!! $def->hasImportButtons() ? $controller->export->fetch() : '' !!}
                    </header>
                    <div>
                        <div class="jarviswidget-editbox"></div>
                        <div class="widget-body no-padding">
                            <form action="{{ $controller->getUrlAction() }}"
                                  method="post"
                                  class="form-horizontal tb-table"
                                  target="submiter" >

                                    <table id="datatable_fixed_column" class="table  table-hover table-bordered">
                                        <thead>
                                            @include('admin::tb.table_thead')
                                        </thead>
                                        <tbody>
                                            @include('admin::tb.table_tbody')
                                        </tbody>
                                        @include('admin::tb.table_tfoot')
                                    </table>
                                    @include('admin::tb.table_pagination')
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    @endif
</section>
@if($def->getCaption())
    <script>
        $(".breadcrumb").html("<li><a href='/admin'>{{__cms('Главная')}}</a></li> <li>{{ $def->getCaption() }}</li>");
        $("title").text("{{ $def->getCaption() }} - {{ __cms(config('builder.admin.caption')) }}");
    </script>
@endif
