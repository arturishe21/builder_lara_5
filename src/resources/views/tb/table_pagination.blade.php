<div class="row tb-pagination">
    <div class="col-sm-4 col-xs-12 hidden-xs">
          <div id="dt_basic_info" class="dataTables_info" role="status" aria-live="polite">
            {{__cms('Показано')}}
          <span class="txt-color-darken listing_from">{{ $rows->count() }}</span>
            -
          <span class="txt-color-darken listing_to">{{ $rows->currentPage() }}</span>
             {{__cms('из')}}
          <span class="text-primary listing_total">{{ $rows->total() }}</span>
             {{__cms('записей')}}
          </div>
    </div>

    <div class="col-sm-8 text-right">
        <div class="dataTables_paginate paging_bootstrap_full">
            {{$rows->appends(request()->all())->links()}}
            
            @if(!empty($def->getPaginationQuantityButtons()))
                @include('admin::tb.pagination_show_amount')
            @endif
        </div>
    </div>
</div>

