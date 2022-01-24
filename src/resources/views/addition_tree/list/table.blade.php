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
                <form
                        action="{{$list->getUrlAction()}}"
                        method="post"
                        class="form-horizontal tb-table"
                        target="submiter" >
                <table id="tb-tree-table" class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-left">
                            <?php
                            $ancestors = $current->getAncestorsAndSelf(); ?>

                            @foreach ($ancestors as $ancestor)
                                <a href="?node={{ $ancestor->id }}" style="color: #fff" class="node_link">{{ $ancestor->t('title')}}</a> /
                            @endforeach
                                <a onclick="TableBuilder.getEditForm(<?=$current->id?>, $(this));" style="min-width: 70px; float: right">{{__cms('Редактировать')}}</a>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tree-td tree-dark" style="padding: 0px; vertical-align: top;text-align: left;">

                                    <table  class="table  table-hover table-bordered">
                                        <thead>
                                        @include('admin::addition_tree.list.head')
                                        </thead>
                                        <tbody class="ui-sortable">
                                        @include('admin::addition_tree.list.body')
                                        </tbody>

                                    </table>
                                    @include('admin::addition_tree.list.pagination')

                            </td>
                        </tr>
                    </tbody>
                </table>
                </form>

            </div>
        </article>
    </div>
</section>

<script>

    TableBuilder.optionsInit({
        action_url: '{{ $list->getUrlAction() }}'
    });

    TableBuilder.action_url = '{{ $list->getUrlAction() }}';

    Tree.parent_id = '{{ $current->id }}';

    showTree = 0;
    $(".show_hide_tree").click(function(){
        $("#tree_top").toggle();

        if($(".show_hide_tree").text() == "{{__cms('Показать дерево')}}") {
            $(".show_hide_tree").text("{{__cms('Спрятать дерево')}}");

            if (showTree == 0) {
                $(".tree_top_content").html("<p style='padding:10px'>{{__cms('Загрузка')}}..</p>");
                $.post("/admin/show-all-tree", {
                        'model' : '<?=addslashes(get_class($current))?>'
                        },
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

    try {
        Tree.sortTable();
    } catch (err) { }

</script>

