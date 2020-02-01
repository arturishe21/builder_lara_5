 <div class="jarviswidget jarviswidget-color-blue " id="wid-id-4" data-widget-editbutton="false" data-widget-colorbutton="false">
    <header>
        <span class="widget-icon"> <i class="fa  fa-file-text"></i> </span>
        <h2> {{__cms('Переводы CMS')}} </h2>
    </header>


     @include("admin::translation_cms.part.table_center")

<div id="modal_wrapper">
   @include("admin::translation_cms.part.pop_trans_add")
</div>
<div class='load_ajax'></div>
<script src="{{asset('packages/vis/builder/translations.js')}}"></script>
<script>

   $(".breadcrumb").html("<li><a href='/admin'>{{__cms("Главная")}}</a></li> <li>{{ __cms('Переводы CMS')}}</li>");
   $("title").text("{{ __cms('Переводы CMS')}} - {{{ __cms(config('builder::admin.caption')) }}}");

    $(document).ready(function(){
        $('.lang_change').editable2({
            url: '/admin/translations_cms/change_text_lang',
            type: 'text',
            pk: 1,
            id: "",
            name: 'username',
            title: 'Enter username'
        });
    });
</script>

 </div>
