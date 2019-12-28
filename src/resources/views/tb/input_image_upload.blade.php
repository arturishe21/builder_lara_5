<div class="pictures_input_field">
    @if ($is_multiple)

        <div class="multi_pictures">
            <div class="progress progress-micro" style="margin-bottom: 0;">
                <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" role="progressbar"
                     style="width: 0%;"></div>
            </div>
            <div class="input input-file">
                @if ($chooseFromUploaded)
                    <span class="button select_with_uploaded" onclick="TableBuilder.selectWithUploadedImages('{{$name}}', 'multi', $(this), '{{$baseName}}', '{{request('id_tree')}}', '{{$path}}')"> {{__cms('Выбрать из загруженных')}} </span>
                @endif
                <span class="button">
                    <input type="file" multiple accept="image/*" class="image_{{$name}}"
                           onchange="TableBuilder.uploadMultipleImages(this, '{{$name}}', '{{$baseName}}', '{{$path}}');">
                    Загрузить
                </span>
                <input type="hidden" name="{{$name}}" value='{{ $value }}'>
                <input type="text" id="{{ $name }}" placeholder="{{__cms('Выберите изображение для загрузки')}}" readonly="readonly">
            </div>
            <div class="tb-uploaded-image-container tb-uploaded-image-container_{{$name}}">
                @if ($source)
                    <ul class="dop_foto">
                        @foreach ($source as $key => $value)
                            @include('admin::tb.html_image')
                        @endforeach
                    </ul>
                @else
                    <ul class="dop_foto"></ul>
                    <div class="no_photo" style="text-align: center; ">
                        {{__cms('Нет изображений')}}
                    </div>
                @endif
                <script>
                    $('.dop_foto').sortable(
                        {
                            items: "> li",
                            update: function (event, ui) {
                                var context = $(this).parent().parent().find("[type=file]");
                                TableBuilder.setInputImages(context);
                            }
                        }
                    );
                </script>
            </div>
            <div style="clear: both"></div>
        </div>

    @else

        <div class="picture_block">
            <div class="progress progress-micro" style="margin-bottom: 0;">
                <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" role="progressbar"
                     style="width: 0%;"></div>
            </div>
            <div class="input input-file">
                @if ($chooseFromUploaded)
                    <span class="button select_with_uploaded" onclick="TableBuilder.selectWithUploadedImages('{{$name}}', 'one_file', $(this), '{{$baseName}}', '{{request('id_tree')}}', '{{$path}}')"> {{__cms('Выбрать из загруженных')}}</span>
                @endif
                <span class="button">
                    <input type="file" accept="image/*" onchange="TableBuilder.uploadImage(this, '{{$name}}', '{{$baseName}}', '{{$path}}');">
                    {{__cms('Загрузить')}}
                </span>
                <input type="text" id="{{ $name }}" placeholder="{{__cms('Выберите изображение для загрузки')}}"
                       readonly="readonly"> <input type="hidden" value="{{$value}}" name="{{ $name }}">
            </div>
            <div class="tb-uploaded-image-container image-container_{{ $name }}">
                @if (isset($value) && $value)
                    @include('admin::tb.html_image_single')
                @else
                    <p style="padding: 20px 0 10px 0">{{__cms('Нет изображения')}}</p>
                @endif
            </div>
        </div>

    @endif

    <div class="modal files_uploaded_table" id ='files_uploaded_table_{{$name}}' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="form-preloader smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close_window" onclick="TableBuilder.closeWindowWithPictures();"> &times; </span>
                    <h4 class="modal-title" id="modal_form_label">{{__cms('Выберите изображения')}}</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-condensed table-hover smart-form has-tickbox">
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <span class="btn btn-success btn-sm" onclick="TableBuilder.selectImageUploaded('{{$name}}', '{{$is_multiple ? 'multi' : 'once'}}')" >{{__cms('Выбрать')}}</span>
                    <span class="btn btn-default"  onclick="TableBuilder.closeWindowWithPictures();"> {{__cms('Отмена')}} </span>
                </div>
            </div>
        </div>
    </div>
</div>
