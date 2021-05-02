
<div class="row tb-pagination">
    <div class="col-sm-4 col-xs-12 hidden-xs">
        <div id="dt_basic_info" class="dataTables_info" role="status" aria-live="polite">
            {{__cms('Показано')}}

            @if ($listingRecords->currentPage() == 1)
                <span class="txt-color-darken listing_from">1</span>
                -
                <span class="txt-color-darken listing_to">{{$listingRecords->currentPage() * $listingRecords->perPage() }} </span>
                {{__cms('из')}}
                <span class="text-primary listing_total">{{$listingRecords->total()}}</span>
                {{__cms('записей')}}

            @else
                <span class="txt-color-darken listing_from">{{ $listingRecords->perPage() * ($listingRecords->currentPage() -1)  }}</span>
                -
                <span class="txt-color-darken listing_to">{{$listingRecords->currentPage() * $listingRecords->perPage() }} </span>
                {{__cms('из')}}
                <span class="text-primary listing_total">{{$listingRecords->total()}}</span>
                {{__cms('записей')}}

            @endif

        </div>
    </div>

    <div class="col-sm-8 text-right">
        <div class="dataTables_paginate paging_bootstrap_full">
            {{$listingRecords->appends(request()->all())->links()}}

            @if ($list->isShowAmount())
                @include('admin::list.pagination_show_amount')
            @endif
        </div>
    </div>
</div>

