<div id="table-preloader" class="smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
<p><a class="show_hide_tree">{{__cms('Показать дерево')}}</a></p>
<div id="tree_top">
    <div class="tree_top_content"></div>
</div>
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
                    <h2>
                        <?php
                        $ancestors = $current::ancestorsAndSelf($current->id); ?>

                        @foreach ($ancestors as $ancestor)
                            <a href="?node={{ $ancestor->id }}" style="color: #fff" class="node_link">{{ $ancestor->title}}</a> /
                        @endforeach

                    </h2>
                    {!! isset($def['buttons']) && $def['buttons'] ?  $controller->buttons->fetch() : '' !!}
                    {!! isset($def['import']) && $def['import']  ? $controller->import->fetch() : '' !!}
                    {!! isset($def['export']) && $def['export'] ? $controller->export->fetch() : '' !!}
                </header>
                <div>
                    <div class="jarviswidget-editbox"></div>
                    <div class="widget-body no-padding">
                        <form
                            action="{{$list->getUrlAction()}}"
                            method="post"
                            class="form-horizontal tb-table"
                            target="submiter" >
                            <table  class="table  table-hover table-bordered">
                                <thead>
                                @include('admin::new.addition_tree.list.head')
                                </thead>
                                <tbody class="ui-sortable">
                                @include('admin::new.addition_tree.list.body')
                                </tbody>

                            </table>
                            @include('admin::new.addition_tree.list.pagination')
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


    Tree.admin_prefix = '{{ config('builder.admin.uri') }}';
    Tree.parent_id = '{{ $current->id }}';

    showTree = 0;
    $(".show_hide_tree").click(function(){
        $("#tree_top").toggle();

        if($(".show_hide_tree").text() == "{{__cms('Показать дерево')}}") {
            $(".show_hide_tree").text("{{__cms('Спрятать дерево')}}");

            if (showTree == 0) {
                $(".tree_top_content").html("<p style='padding:10px'>Загрузка..</p>");
                $.post("/admin/show_all_tree/{{$definition->getNameDefinition()}}", {},
                    function(data){
                        $(".tree_top_content").html(data);
                        Tree.init();
                        showTree = 1;
                    });
            }

        } else {
            $(".show_hide_tree").text("{{__cms('Показать дерево')}}")
        }
    });

    $(".breadcrumb").html("<li><a href='/admin'>{{__cms('Главная')}}</a></li> <li>{{__cms('Структура сайта')}}</li>");
    $("title").text("{{__cms('Структура сайта')}} - {{ __cms('Административная часть сайта')}}");

    try {
        Tree.sortTable();
    } catch (err) { }

</script>

