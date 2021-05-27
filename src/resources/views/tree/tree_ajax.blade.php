
 <div id="table-preloader" class="smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
    <p><a class="show_hide_tree">{{__cms('Показать дерево')}}</a></p>
    <div id="tree_top">
        <div class="tree_top_content"></div>
    </div>
    
    <table id="tb-tree-table" class="table table-bordered">
        <thead>
          <tr>
            <th class="text-left">@include('admin::tree.content_header')</th>
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
 @include('admin::tree.create_modal', ['treeName' => $treeName])

 <script>

    Tree.admin_prefix = '/admin';
    Tree.parent_id = '{{ $current->id }}';

    showTree = 0;
    $(".show_hide_tree").click(function(){
         $("#tree_top").toggle();

         if($(".show_hide_tree").text() == "{{__cms('Показать дерево')}}") {
             $(".show_hide_tree").text("{{__cms('Спрятать дерево')}}");

             if (showTree == 0) {
                $(".tree_top_content").html("<p style='padding:10px'>Загрузка..</p>");
                $.post("/admin/show_all_tree/{{$treeName}}", {},
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
  $("title").text("{{__cms('Структура сайта')}} - {{{ __cms(config('builder.admin.caption')) }}}");

  try {
        Tree.sortTable();
  } catch (err) { }

</script>

