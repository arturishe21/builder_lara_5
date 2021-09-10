<div class="modal files_uploaded_table" id ='files_uploaded_table_{{$field->getNameField()}}' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="form-preloader smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
        <div class="modal-content">
            <div class="modal-header">
                <span class="close_window" onclick="$('.files_uploaded_table').hide()"> &times; </span>
                <h4 class="modal-title" id="modal_form_label">{{__cms('Выберите файлы')}}</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-condensed table-hover smart-form has-tickbox">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="name">{{__cms('Имя')}}</th>
                        <th class="type">{{__cms('Тип')}}</th>
                        <th class="size">{{__cms('Размер')}}</th>
                        <th class="date">{{__cms('Дата')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: #fff">
                <span class="btn btn-success btn-sm" onclick="TableBuilder.selectFilesUploaded('{{$field->getNameField()}}', '{{$isMultiple ? 'multi' : 'once'}}')" >{{__cms('Выбрать')}}</span>
                <span class="btn btn-default"  onclick="$('.files_uploaded_table').hide()"> {{__cms('Отмена')}} </span>
            </div>
        </div>
    </div>
</div>
