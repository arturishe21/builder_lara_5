 <script>
   $(".breadcrumb").html("<li><a href='/admin'>{{__cms('Главная')}}</a></li> <li>{{__cms($title)}}</li>");
   $("title").text("{{__cms($title)}} - {{ __cms(config('builder.admin.caption')) }}");
 </script>

 <!-- MAIN CONTENT -->
<div class="settings">
    <div class="row settings_row">
         <div class="jarviswidget jarviswidget-color-blue " id="wid-id-4" data-widget-editbutton="false" data-widget-colorbutton="false">
        <header>
            <span class="widget-icon"> <i class="fa  fa-file-text"></i> </span>
            <h2> {{__cms($title)}} </h2>
             @if(isset($groups))
                  <div class="btn-group">
                       <button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
                          <span>{{__cms('Все')}}</span>  <i class="fa fa-caret-down"></i>
                       </button>
                       <ul class="dropdown-menu js-status-update pull-right">
                            <li {{!request('group')?'class="active"':''}}><a href="{{route('m.show_settings')}}">{{__cms('Все')}}</a></li>
                        @foreach($groups as $k=>$el)
                            <li {{request('group')=="$k" ? 'class="active"' : ''}} >
                              <a href="{{route('m.show_settings',["group"=>$k])}}">{{__cms($el)}}</a>
                            </li>
                        @endforeach
                       </ul>
                  </div>
                @endif
        </header>
        <div class="table_center no-padding">
            @include("admin::settings.part.table_center")
        </div>
      </div>
    </div>
</div>

<!-- END MAIN CONTENT -->
<div id="modal_wrapper">
   @include("admin::settings.part.pop_settings_add")
</div>
<link rel="stylesheet" type="text/css" href="/packages/vis/builder/css/settings.css">
<script src="/packages/vis/builder/js/settings.js"></script>
