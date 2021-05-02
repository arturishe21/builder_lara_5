
<div id="table-preloader" class="smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
<p><a class="show_hide_tree">{{__cms('Показать дерево')}}</a></p>
<div id="tree_top">
    <div class="tree_top_content"></div>
</div>

<table id="tb-tree-table" class="table table-bordered">
    <thead>
    <tr>
        <th class="text-left">@include('admin::tree.header')</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tree-td tree-dark" style="padding: 0px; vertical-align: top;text-align: left;">
            {!! $content !!}
        </td>
    </tr>
    </tbody>
</table>
@include('admin::tree.create', ['treeName' => $treeName])

<script>
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

