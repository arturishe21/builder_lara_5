<div class="table_center no-padding">
    <div class="dt-toolbar">
      <div class="col-xs-12 col-sm-6">
          <div id="dt_basic_filter" class="dataTables_filter">
           <form action="" method="get" id="search_form">
              <label>
                  <span class="input-group-addon">
                  <i class="glyphicon glyphicon-search"></i>
                  </span>
                  <input class="form-control" name="search_q" type="search" value="{{$search_q}}" aria-controls="dt_basic">
              </label>
             </form>
          </div>
      </div>
      <div class="col-sm-6 col-xs-12 hidden-xs">
          <div id="dt_basic_length" class="dataTables_length">
              <label>

                  <select class="form-control" name="dt_basic_length" aria-controls="dt_basic">

                  @foreach([20, 40, 100] as $val)
                      <option value="{{$val}}"
                        @if($val == $count_show)
                         selected
                        @endif
                       >{{$val}}</option>
                  @endforeach
                  </select>
              </label>
          </div>
     </div>
    </div>
  <table class="table  table-hover table-bordered " id="sort_t">
     <thead>
         <tr>
             <th style="width: 25%">{{__cms('Фраза')}}</th>
             <th>{{__cms("Код")}}</th>
             <th>{{__cms("Переводы")}}</th>
             <th style="width: 80px">
                 <a class="btn btn-sm btn-success" categor="0" onclick="Trans.getCreateForm(this);">
                     {{__cms("Добавить")}}
                 </a>
             </th>
         </tr>
     </thead>
     <tbody>
       @forelse($phrases as $phrase )
            <tr class="tr_{{$phrase->id}} " id_page="{{$phrase->id}}">

                <td style="text-align: left;">
                    {{$phrase->phrase}}
                </td>
                <td>__cms("{{$phrase->phrase}}")</td>

                <td style="text-align: left">

                 <?php
                    $trans = $phrase->getTrans();
                    ?>

                 @foreach($langs as $languageKey=>$languageValue)
                    <p>
                    <img class="flag flag-{{str_replace(['en', 'uk'],  ['us', 'ua'], $languageKey)}}" style="margin-right: 5px">
                     <a data-type="textarea" class="lang_change" data-pk="{{$phrase->id}}"
                        data-name="{{$languageKey}}"
                        data-original-title="Язык: {{$languageValue}}">{{$trans[$languageKey] ?? ''}}</a>
                     </p>
                  @endforeach
                </td>
                <td>
                    <div class="btn-group hidden-phone pull-right">
                        <a class="btn dropdown-toggle btn-xs btn-default"  data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu pull-right" id_rec ="{{$phrase->id}}">
                             <li>
                                 <a onclick="Trans.doDelete({{$phrase->id}});"><i class="fa red fa-times"></i> {{__cms("Удалить")}}</a>
                             </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
              <tr>
                 <td colspan="5"  class="text-align-center">
                     {{__cms("Каталог пустой")}}
                  </td>
             </tr>
        @endforelse
    </tbody>
</table>
     <div class="dt-toolbar-footer">
      <div class="col-sm-6 col-xs-12 hidden-xs">
          <div id="dt_basic_info" class="dataTables_info" role="status" aria-live="polite">
            {{__cms("Показано")}}
          <span class="txt-color-darken listing_from">{{$phrases->firstItem()}}</span>
            -
          <span class="txt-color-darken listing_to">{{$phrases->lastItem()}}</span>
             {{__cms("из")}}
          <span class="text-primary listing_total">{{$phrases->total()}}</span>
            {{__cms("записей")}}
          </div>
      </div>
      <div class="col-xs-12 col-sm-6">
        <div id="dt_basic_paginate" class="dataTables_paginate paging_simple_numbers">
            {{$phrases->links()}}
        </div>
      </div>
</div>
