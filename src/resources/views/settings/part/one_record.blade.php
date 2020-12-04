<td style="text-align: left;">
    <a onclick="Settings.getEdit({{$el->id}})">{{$el->title}}</a>
</td>
<td><span class="select_text">setting('{{$el->slug}}')</span></td>
<td>{{__cms(config('builder.settings.type')[$el->type])}}</td>
<td>{{__cms(isset(config('builder.settings.groups')[$el->group_type])? config('builder.settings.groups')[$el->group_type] : "")}}</td>
<td>
  @if($el->type==1 || $el->type==6)
        <a onclick="Settings.getEdit({{$el->id}})">{{__cms('Текстовое поле')}}</a>
  @elseif($el->type==2)
        <a onclick="Settings.getEdit({{$el->id}})">{{__cms('Список')}}</a>
  @elseif($el->type==3)
        <a onclick="Settings.getEdit({{$el->id}})">{{__cms('Двойной список')}} </a>
   @elseif($el->type==5)
         <a onclick="Settings.getEdit({{$el->id}})">{{__cms('Тройной список')}}</a>
  @elseif($el->type==4)
        <a href="{{$el->value}}" target="_blank">{{basename($el->value)}}</a>
  @elseif($el->type==7)

        <span class="onoffswitch">
            <input onchange="Settings.activeToggle('{{$el->id}}', this.checked);"
                   type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"  id="myonoffswitch{{$el->id}}"
                  {{$el->value == 1 ? 'checked' : ''}}
            >
            <label class="onoffswitch-label" for="myonoffswitch{{$el->id}}">
                <span class="onoffswitch-inner" data-swchon-text="ДА" data-swchoff-text="НЕТ"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </span>

  @else
        <span class="dblclick-edit selectable"
              data-type="text"
              data-pk="{{$el->id}}"
              data-url="/admin/settings/fast_edit"
              data-name="value"
              data-title="Введите:"
        >{{$el->value}}</span>
        <div class="fast-edit-buttons">
            <div class="input_field">
                <div class="div_input">
                    <div class="input_content">
                        <label class="input">
                            <input class="dblclick-edit-input form-control input-sm unselectable settings_fast_edit_input" value="{{$el->value}}" name="title_{{$el->id}}" type="text" placeholder="Введите значение" >
                        </label>
                    </div>
                </div>
            </div>
            <span class="fa fa-save" onclick="Settings.saveFastEdit(this, {{$el->id}});"></span>
            <i class="glyphicon glyphicon-remove btn-cancel" onclick="TableBuilder.closeFastEdit(this, 'cancel');"></i>
        </div>
  @endif
</td>
<td>
 <div style="display: inline-block">
    <div class="btn-group hidden-phone pull-right">
        <a class="btn dropdown-toggle btn-default"  data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
        <ul class="dropdown-menu pull-right" id_rec ="{{$el->id}}">
             <li>
                <a class="edit_record" onclick="Settings.getEdit({{$el->id}})"><i class="fa fa-pencil"></i> {{__cms('Редактировать')}}</a>
             </li>
        </ul>
    </div>
  </div>
</td>
