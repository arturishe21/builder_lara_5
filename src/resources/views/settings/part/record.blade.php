<td style="text-align: left;">
    <a onclick="Settings.getEdit({{$el->id}})">{{$el->title}}</a>
</td>
<td><span class="select_text">setting('{{$el->slug}}')</span></td>
<td>{{__cms(config('builder.settings.type')[$el->type])}}</td>
<td>{{__cms(isset(config('builder.settings.groups')[$el->group_type])? config('builder.settings.groups')[$el->group_type] : "")}}</td>
<td>
  @if($el->type==1 || $el->type==6)
        <a onclick="Settings.getEdit({{$el->id}})">{{__cms('Текстовое поле')}}</a>
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
              data-url="/admin/settings/fast-save/{{$el->id}}"
              data-name="value"
              data-title="Введите:"
        >{{$el->value}}</span>

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
