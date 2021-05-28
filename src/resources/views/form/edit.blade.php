<!-- Modal -->
<div class="modal fade modal_form_{{$definition->model()->getTable()}}" id="modal_form_edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" style="width: 920px" data-width="920px">
        <div class="form-preloader smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-default close_button" style="float: right; margin: 0 5px"  type="button"
                        onclick="TableBuilder.doClosePopup('{{$definition->model()->getTable()}}')"
                > {{__cms('Отмена')}} </button>
                @if (app('user')->hasAccessActionsForCms('save'))
                <button class="btn btn-success btn-sm" style="float: right" type="button"
                        onclick="$('#edit_form_{{$definition->getNameDefinition()}}').submit();">
                    <span class="glyphicon glyphicon-floppy-disk"></span>
                    {{__cms('Сохранить')}}
                </button>
                @endif

                <h4 class="modal-title" id="modal_form_label">{{__cms('Редактирование')}}</h4>
            </div>
            @include('admin::form.create_center', ['type' => 'edit'])
        </div>
    </div>
</div>

@include('admin::form.create_validation', ['type' => 'edit'])
