<div class="tb-tree-content-inner">
    <div class="smart-form">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="width: 10px"></th>
                <th>{{__cms('Название')}}</th>
                <th>{{ __cms('Шаблон') }}</th>
                <th>Slug</th>
                <th style="width: 60px">{{__cms('Активный')}}</th>

                <th style="width: 80px">
                    @if (app('user')->hasAccessActionsForCms('insert'))
                    <a href="javascript:void(0);" onclick="Tree.showCreateForm('{{$current->id}}');" style="min-width: 70px;" class="btn btn-success btn-sm">{{__cms('Добавить')}}</a>
                    @endif
                </th>
            </tr>
            </thead>
            <tbody class="ui-sortable" id="wid-id-1">

            @if($current->parent_id)
                <tr>
                    <td colspan="6">
                        <a href="?node={{$current->parent_id}}" class="node_link">&larr; {{__cms('Назад')}}</a>
                    </td>
                </tr>
            @endif
            @foreach($children as $item)
                @include('admin::tree.row')
            @endforeach
            </tbody>

            <tfoot>
            </tfoot>

        </table>
        <div class="row tb-pagination">
            <div class="col-sm-4 col-xs-12 hidden-xs">
                <div id="dt_basic_info" class="dataTables_info" role="status" aria-live="polite">
                    {{__cms('Показано')}}
                    <span class="txt-color-darken listing_from">{{$children->count()}}</span>
                    -
                    <span class="txt-color-darken listing_to">{{$children->currentPage()}}</span>
                    {{__cms('из')}}
                    <span class="text-primary listing_total">{{$children->total()}}</span>
                    {{__cms('записей')}}
                </div>
            </div>

            <div class="col-sm-8 text-right">
                <div class="dataTables_paginate paging_bootstrap_full">
                    {{$children->appends(request()->all())->links()}}

                    @if (is_array(config('builder.'.$treeName.'.pagination.per_page')))
                        @include('admin::tree.partials.pagination_show_amount')
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var action_url = "/admin/actions/{{$treeName}}";

            TableBuilder.optionsInit({
                action_url: action_url
            });

            TableBuilder.action_url = action_url;
            $('.tpl-editable').editable2({
                url: window.location.href,
                source: [
                        @foreach ($templates as $slug => $template)
                             { value: '{{$slug}}', text: '{{$template}}' },
                        @endforeach
                ],
                display: function(value, response) {
                    return false;   //disable this method
                },
                success: function(response, newValue) {
                    $(this).html('$' + newValue);
                },
                params: function(params) {
                    //originally params contain pk, name and value
                    params.query_type = 'do_change_template';
                    return params;
                }
            });
        });
    </script>
</div>
