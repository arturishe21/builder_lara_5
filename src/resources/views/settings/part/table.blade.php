<table class="table  table-hover table-bordered " id="sort_t">
     <thead>
         <tr>
             <th style="width: 25%">{{__cms('Название')}}</th>
             <th>{{__cms('Код для вставки')}}</th>
             <th>{{__cms('Тип')}}</th>
              <th>{{__cms('Группа')}}</th>
             <th>{{__cms('Значение')}}</th>
             <th style="width: 80px">
              <a class="btn btn-sm btn-success" onclick="Settings.getCreateForm(this);">
               {{__cms('Создать')}}
              </a>
             </th>
         </tr>
     </thead>
     <tbody >
       @forelse($data as $k=>$el )
            <tr class="tr_{{$el->id}} " id_page="{{$el->id}}">
                @include("admin::settings.part.record")
            </tr>
        @empty
              <tr>
                 <td colspan="5"  class="text-align-center">
                     {{__cms('Каталог пустой')}}
                  </td>
             </tr>
        @endforelse
    </tbody>
</table>
<div class="dt-toolbar-footer">
      <div class="col-sm-4 col-xs-12 hidden-xs">
          <div id="dt_basic_info" class="dataTables_info" role="status" aria-live="polite">
            {{__cms('Показано')}}
          <span class="txt-color-darken listing_from">{{$data->firstItem()}}</span>
            -
          <span class="txt-color-darken listing_to">{{$data->lastItem()}}</span>
           {{__cms('из')}}
          <span class="text-primary listing_total">{{$data->total()}}</span>
            {{__cms('записей')}}
          </div>
      </div>
      <div class="col-xs-12 col-sm-8">
        <div id="dt_basic_paginate" class="dataTables_paginate paging_simple_numbers">
          {{$data->appends(array('group' => request('group')))->links()}}
        </div>
      </div>
</div>

