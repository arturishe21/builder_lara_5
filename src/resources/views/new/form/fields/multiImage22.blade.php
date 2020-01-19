444444444

<section class="section_field">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <div class="multi_pictures">
                    <div class="progress progress-micro" style="margin-bottom: 0;">
                        <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" role="progressbar"
                             style="width: 0%;"></div>
                    </div>
                    <div class="input input-file">
                        <span class="button select_with_uploaded" onclick="TableBuilder.selectWithUploadedImages('{{$field->getNameField()}}', 'multi', $(this), '{{$field->getNameField()}}', '{{request('id_tree')}}')"> {{__cms('Выбрать из загруженных')}} </span>
                        <span class="button">
                            <input type="file" multiple accept="image/*" class="image_{{$field->getNameField()}}"
                                onchange="TableBuilder.uploadMultipleImages(this, '{{$field->getNameField()}}', '{{$field->getNameField()}}');">
                                 Загрузить
                        </span>
                        <input type="hidden" name="{{$field->getNameField()}}" value='{{ $field->getValue() }}'>
                        <input type="text" id="{{ $field->getNameField() }}" placeholder="{{__cms('Выберите изображение для загрузки')}}" readonly="readonly">
                    </div>
                    <div class="tb-uploaded-image-container tb-uploaded-image-container_{{$field->getNameField()}}">
                        @if ($field->getValue())
                            <ul class="dop_foto">
                                @foreach ($field->getValueArray() as $key => $value)
                                    @include('admin::new.form.fields.partials.images_multi')
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
            </div>
        </div>
    </div>
</section>


