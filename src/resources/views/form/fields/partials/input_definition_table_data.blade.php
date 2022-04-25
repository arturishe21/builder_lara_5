<?php
    $paramsJson = json_decode(request('paramsJson'));
?>

<div class="loader_definition"><i class="fa fa-gear fa-4x fa-spin"></i></div>
<table class="table table-hover table-bordered">
    <thead>
    <tr>
        <td class="col_sort"></td>
        @foreach($fieldsDefinition as $field)
            <th>{{$field->getName()}}</th>
        @endforeach
        <th style="width: 10%"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($list as $record)
        <tr data-id="{{$record->id}}" id="sort-{{$record->id}}">
            <td class="handle col_sort"><i class="fa fa-sort"></i></td>
            @foreach($record->fields as $field)
                <td>{!! $field->value !!}</td>
            @endforeach
            <td>
                <div class="btn-group hidden-phone pull-right">
                    <a class="btn dropdown-toggle btn-default"  data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a onclick="ForeignDefinition.edit({{$record->id}}, '{{request('id')}}', '{{$attributes}}', 'actions')"><i class="fa fa-pencil"></i> {{__cms('Редактировать')}}</a></li>
                       {{-- <li><a onclick="ForeignDefinition.clone({{$record->id}}, '{{request('id')}}', '{{$attributes}}', 'actions')"><i class="fa fa-copy"></i> {{__cms('Клонировать')}}</a></li>--}}
                        <li><a onclick="ForeignDefinition.delete({{$record->id}}, '{{request('id')}}', '{{$attributes}}', '{{$urlAction}}')"><i class="fa red fa-times"></i> {{__cms('Удалить')}}</a></li>
                    </ul>
                </div>

            </td>
        </tr>
    @empty
        <tr><td colspan="{{count ($fieldsDefinition) + 1 }}"> {{__cms('Пока пусто')}} </td></tr>
    @endforelse
    </tbody>
</table>

<div style="text-align: center; padding-top: 10px" class="paginator_definition paginator_pictures">
    <div @if($definitionRelation->getIsShowPerPage()) style="float: left" @endif  >
        {{ $list->render() }}
    </div>

    @if ($definitionRelation->getIsShowPerPage())
        <div class="show_amount" style="float: right">
            <span>{{__cms('Показывать по')}}:</span>
            <div class="btn-group">
                @foreach ($perPage as $countItem)
                    <button type="button"
                            onclick="setPerPageAmount{{$paramsJson->name}}('{{$countItem}}');"
                            class="btn btn-default btn-xs {{ $countItem == $count? 'active' : ''}}">
                        {{$countItem}}
                    </button>
                @endforeach
            </div>
        </div>
    @endif
    <div style="clear: both"></div>
</div>

<script>

    function setPerPageAmount{{$paramsJson->name}}(count) {
        getPages{{$paramsJson->name}}('/admin/{{$urlAction}}?count=' + count);
    }

    $('.paginator_definition a').click(function (e) {
        var url = $(this).attr('href');

        getPages{{$paramsJson->name}}(url);

        e.preventDefault();
    });

    function getParameterByName(name, url = window.location.href) {

        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    function getPages{{$paramsJson->name}}(url) {

        var page = getParameterByName('page', url);

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                'id' : '{{request('id')}}',
                'paramsJson' : '{!! addslashes(request('paramsJson')) !!}',
                'query_type' : 'get_html_foreign_definition',
            },
            dataType: 'json',
            success: function (response) {
                if (response.html) {

                    $('.definition_{{$paramsJson->name}}').html(response.html);

                    @if (isset($paramsJson->sortable))
                        $('.definition_{{$paramsJson->name}} tbody').sortable({
                            handle: ".handle",
                            update: function ( event, ui ) {
                                ForeignDefinition.changePosition($(this), '{!! addslashes(request('paramsJson')) !!}', page);
                            }
                        });
                    @else
                    $('.definition_{{$paramsJson->name}} .col_sort').hide();
                    @endif
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var errorResult = jQuery.parseJSON(xhr.responseText);

                TableBuilder.showErrorNotification(errorResult.message);
            }
        });
    }

</script>