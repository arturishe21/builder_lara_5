<div class="files_type_fields">
    @if ($is_multiple)
        <div class="multi_files">
            <div class="progress progress-micro" style="margin-bottom: 0;">
                <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" style="width: 0%;" role="progressbar"></div>
            </div>
            <div class="input input-file">
            <span class="button">
                <input type="file"  multiple onchange="TableBuilder.uploadFileMulti(this, '{{$name}}', '{{$path}}');" {{$accept ? "accept=$accept" : ""}}>
                {{__cms('Загрузить')}}
            </span>
                @if ($chooseFromUploaded)
                    <span class="button select_with_uploaded" onclick="TableBuilder.selectWithUploaded('{{$name}}', 'multi_file', $(this), '{{$path}}' )">
               {{__cms('Выбрать из загруженных')}}
             </span>
                @endif
                <input type="hidden" name="{{$name}}" value='{{$value}}'>
                <input type="text"
                       id="{{ $name }}"
                       value=""
                       placeholder="{{__cms('Выберите файлы для загрузки')}}"
                       readonly="readonly">
            </div>

            @if (isset($comment) && $comment)
                <div class="note">
                    {{$comment}}
                </div>
            @endif


            <div class="tb-uploaded-file-container-{{$name}} uploaded-files">
                <ul>
                    @if(isset($source) && is_array($source))
                        @foreach($source as $file)
                            <li>
                                {{basename($file)}} <a href="{{$file}}" path = "{{$file}}" target="_blank">{{__cms('Скачать')}}</a>
                                <a class="delete" onclick="TableBuilder.doDeleteFile(this)">{{__cms('Удалить')}}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
                <script>
                    TableBuilder.doSortFileUpload();
                </script>
            </div>
        </div>
    @else
        <div class="progress progress-micro" style="margin-bottom: 0;">
            <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" style="width: 0%;" role="progressbar"></div>
        </div>
        <div class="input input-file">
             <span class="button">
                 <input type="file" onchange="TableBuilder.uploadFile(this, '{{$name}}', '{{$path}}');" {{$accept ? "accept=$accept" : ""}}>
                 {{__cms('Загрузить')}}
             </span>
            @if ($chooseFromUploaded)
                <span class="button select_with_uploaded" onclick="TableBuilder.selectWithUploaded('{{$name}}', 'one_file', $(this), '{{$path}}')">
                {{__cms('Выбрать из загруженных')}}
             </span>
            @endif
            <input type="text"
                   id="{{ $name }}"
                   name="{{ $name }}"
                   value="{{ $value }}"
                   placeholder="@if($value) {{$value}} @else {{__cms('Выберите файл для загрузки')}}  @endif"
                   readonly="readonly">
        </div>

        @if (isset($comment) && $comment)
            <div class="note">
                {{$comment}}
            </div>
        @endif

        <div class="tb-uploaded-file-container-{{$name}} tb-uploaded-file-container">
            @if ($value)
                <a href="{{url($value)}}" target="_blank">{{__cms('Скачать')}}</a> |
                <a class="delete" style="color:red;" onclick="$(this).parents('.files_type_fields').find('input[type=text]').val(''); $(this).parent().hide()">{{__cms('Удалить')}}</a>
            @endif
        </div>
@endif

<!-- Modal -->
    <div class="modal files_uploaded_table" id ='files_uploaded_table_{{$name}}' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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
                <div class="modal-footer">
                    <span class="btn btn-success btn-sm" onclick="TableBuilder.selectFilesUploaded('{{$name}}', '{{$is_multiple ? 'multi' : 'once'}}')" >{{__cms('Выбрать')}}</span>
                    <span class="btn btn-default"  onclick="$('.files_uploaded_table').hide()"> {{__cms('Отмена')}} </span>
                </div>
            </div>
        </div>
    </div>

</div>

