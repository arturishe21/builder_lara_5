   <style>
    .types{
        display: none;
    }
   </style>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        @if(isset($info->id))
            <h4 class="modal-title" id="modal_form_label">{{__cms('Редактирование')}}</h4>
        @else
            <h4 class="modal-title" id="modal_form_label">{{__cms('Создание')}}</h4>
        @endif
      </div>
      <div class="modal-body">

        <form id="form_page" class="smart-form" enctype="multipart/form-data" method="post" novalidate="novalidate" >
          <fieldset style="padding:0">
                <div class="row">
                  <section class="col col-6">
                    <label class="label" for="title">{{__cms('Название')}}</label>
                    <div style="position: relative;">
                      <label class="input">
                      <input type="text" id="title" value="{{ $info->title ?? '' }}" name="title"
                        class="dblclick-edit-input form-control input-sm unselectable">
                      </label>
                    </div>
                  </section>
                  <section class="col col-6">
                    <label class="label" for="slug">{{__cms('Код(для вставки)')}}</label>
                    <div style="position: relative;">
                      <label class="input">
                      <input type="text" id="slug" value="{{ $info->slug ?? '' }}"  name="slug"
                        {{ isset($info->slug) ? "readonly" : "" }}
                        class="dblclick-edit-input form-control input-sm unselectable">
                      </label>
                    </div>
                  </section>
           </div>
            <div class="row">
                 <section class="col" style="float: none">
                     <label class="label">{{__cms('Группа')}}</label>
                     <div style="position: relative;">
                       <label class="select">
                          <select name="group">
                              @foreach(config('builder.settings.groups') as $slug=>$value)
                                  <option value="{{$slug}}" {{ isset($info->group_type) && $slug == $info->group_type ? "selected" : "" }}>{{__cms($value)}}</option>
                              @endforeach
                          </select>
                          <i></i>
                       </label>
                     </div>
                   </section>
              </div>
            <div class="row">
               <section class="col" style="float: none">
                   <label class="label">{{__cms('Тип')}}</label>
                   <div style="position: relative;">
                     <label class="select">

                        <select name="type" onchange="Settings.typeChange(this)">
                            @foreach(config('builder.settings.type') as $slug => $value)
                                <option value="{{$slug}}" {{ isset($info->type) && $slug==$info->type ? "selected":"" }}>{{__cms($value)}}</option>
                            @endforeach
                        </select>
                        <i></i>
                     </label>
                   </div>
                 </section>
            </div>

             <div class="row">
                <section class="col" style="float: none">
                   @include("admin::settings.partials.type_input")
                   @include("admin::settings.partials.type_textarea")


                    <div class='type_4 types' {!! isset($info->type) && $info->type==4?'style="display: block"':"" !!}>
                        <label class="label" >{{__cms('Значение')}}</label>
                        <div class="input input-file">
                            <span class="button"><input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">{{__cms('Выбрать')}}</span>
                            <input type="text" placeholder="{{__cms('Выбрать файл для загрузки')}}" readonly="">
                        </div>
                        @if(isset($info->value) && isset($info->type) && $info->type==4)
                            <p><a href='{{$info->value}}' target='_blank'>{{$info->value}}</a></p>
                        @endif

                    </div>
                    @include("admin::settings.partials.type_textarea_froala")
                    <div class='type_7 types'
                            {!!  isset($info->type) && $info->type==7 ? 'style=display: block' :""!!}
                    >
                          <label class="label" >{{__cms('Значение')}}</label>
                          <label class="toggle" style="padding-right: 51px">
                              <input type="hidden" value="0" name="status">
                              <input type="checkbox" {{isset($info->value) && $info->value == 1 ? "checked" : ""}} value="1" name="status">
                              <i data-swchoff-text="ВЫКЛ" data-swchon-text="ВКЛ"></i>
                          </label>
                    </div>

                </section>
             </div>

          </fieldset>
                <div class="modal-footer">
                  <i class="fa fa-gear fa-41x fa-spin" style="display: none"></i>
                  <button  type="submit" class="btn btn-success btn-sm"> <span class="glyphicon glyphicon-floppy-disk"></span> {{__cms('Сохранить')}} </button>
                  <button type="button" class="btn btn-default" data-dismiss="modal"> {{__cms('Отмена')}} </button>
                </div>

                <input type="hidden" name="id" value="{{$info->id ?? ''}}">
        </form>
      </div>
 <script>
    @if(!isset($info->id))
         $("#form_page [name=title]").keyup(function(){
             $("#form_page [name=slug]").val(slug_gen($("#form_page [name=title]").val()));
         })
    @endif;
 </script>
