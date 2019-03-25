@if($def->hasAction('filter'))
     <form name="filter">
       <table>
        <tr>
            <td>
                {{ $def->getAction()['filter']['caption'] ?? ""}}
            </td>
            @if(isset($def->getAction()['filter']['fields']) && count($def->getAction()['filter']['fields']))
                @foreach($def->getAction()['filter']['fields'] as $field)
                     <td>
                       <section>
                           <div style="position: relative;">
                               <label class="input">
                                   <select {{isset($field['width']) ? "style='width:".$field['width']."px'" : ""}} class="dblclick-edit-input form-control input-small unselectable" name="filter[{{$field['field']}}]">
                                       <option  value="">{{$field['caption']}}</option>

                                       @foreach($field['options'] as $k => $value)
                                           <option  value="{{$k}}">{{$value}}</option>
                                       @endforeach
                                   </select>
                               </label>
                           </div>
                         </section>
                        </td>
                @endforeach
            @endif
        </tr>
       </table>
    </form>
    <script>
        $("form[name=filter] select").change(function(){
            var urlFilter = $("form[name=filter]").serialize();
            doAjaxLoadContent(window.location.pathname);
        });
    </script>
@endif
