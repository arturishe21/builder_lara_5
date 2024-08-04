<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <div class="files_type_fields">
                    <div class="progress progress-micro" style="margin-bottom: 0;">
                        <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" style="width: 0%;" role="progressbar"></div>
                    </div>
                    <div class="input input-file">

                         <span class="button">
                             <input type="file" onchange="TableBuilder.uploadFile(this, '{{$field->getNameField()}}');"  {!! $field->getAccept() !!}
                             data-name-model="{{$definition->getFullPathDefinition()}}"
                             >
                             {{__cms('Загрузить')}}
                         </span>

                        @if ($field->checkSelectionFiles())
                         <span class="button select_with_uploaded"
                               data-name-model = "{{$definition->getFullPathDefinition()}}"
                               onclick="TableBuilder.selectWithUploaded('{{$field->getNameField()}}', 'one_file', $(this))"
                               style="right: 20px"
                         >
                            {{__cms('Выбрать из загруженных')}}
                         </span>
                        @endif

                        <input type="text"
                               id="{{ $field->getNameField() }}"
                               name="{{ $field->getNameField() }}"
                               value="{{ $field->getValue() }}"
                               placeholder="{{$field->getValue() ?: __cms('Выберите файл для загрузки')}}"
                               readonly="readonly">
                    </div>

                    @if ($field->getComment())
                        <div class="note">
                            {{$field->getComment()}}
                        </div>
                    @endif

                    <div class="tb-uploaded-file-container-{{$field->getNameField()}} tb-uploaded-file-container">
                        @if ($field->getValue())
                            <a href="{{url($field->getValue())}}" target="_blank">{{__cms('Скачать')}}</a> |
                            <a class="delete" style="color:red;" onclick="$(this).parents('.files_type_fields').find('input[type=text]').val(''); $(this).parent().hide()">{{__cms('Удалить')}}</a>
                        @endif
                    </div>
                    
                    @include('admin::form.fields.partials.select_files', ['isMultiple' => false])

                </div>

            </div>


        </div>
    </div>
</section>
