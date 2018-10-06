<div id="{{$name}}" data-interface="sortable" class="sortable-wrapper">
    <div class="sortable-values" style="width: 100%; display: block; border-top: 3px solid #000; border-bottom: 1px solid #000; border-right: none; max-height: 400px; overflow: auto">
        @foreach($list as $optionId => $option)
            <div class="sortable-item" data-value="{{ $optionId }}" style="display: table; border-collapse: collapse; cursor: move; background-color: #fff;">

                @if($is_sortable)
                    <div class="sortable-handler" style="display: table-cell; padding: 5px 10px;background-color: #ddd; text-align: center;"><i class="fa fa-arrows-v"></i></div>
                @endif

                @if(is_array($option))
                    @foreach($option as $colKey => $colValue)
                        <div style="display: table-cell; border: 1px solid #ddd; border-collapse: collapse; padding: 5px 10px; @if($main_field && $colKey == $main_field) width: 100% @endif">{{ $colValue }}</div>
                    @endforeach
                @else
                    <div style="display: table-cell; border: 1px solid #ddd; border-collapse: collapse; padding: 5px 10px; width: 100%">{{$option}}</div>
                @endif

                @if($optionActivation)
                    <div style="display: table-cell; border: 1px solid #ddd; border-collapse: collapse; padding: 5px 10px; border-left: 1px solid #ddd">
                        <input
                            type="checkbox"
                            data-option-activation
                            @if( ($is_checked && !$fieldValue) || in_array($optionId, $fieldValue))
                                checked
                            @endif
                        >
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <input type="hidden" data-sortable-result name="{{ $name }}" value="{{ join(',', $fieldValue) }}">
</div>

<script>
    TableBuilder.initSortableValue($('#{{$name}}'));
</script>