<section class="{{$field->getClassName()}}">
    <label class="label" for="{{ $field->getNameField()}}">{{$field->getName()}}</label>
    <div style="position: relative;">
        <div class="div_input">
            <div class="input_content">

                <div class="files_type_fields">
                    <div class="multi_files">
                        <div class="progress progress-micro" style="margin-bottom: 0;">
                            <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" style="width: 0%;" role="progressbar"></div>
                        </div>
                        <div class="input input-file">
                            <span class="button">
                                <input type="file"  multiple onchange="TableBuilder.uploadFileMulti(this, '{{$field->getNameField()}}');" {!! $field->getAccept() !!}>
                                {{__cms('Загрузить')}}
                            </span>
                            <span class="button select_with_uploaded"
                                  style="right: 20px"
                                  data-name-model = "{{$definition->getFullPathDefinition()}}"
                                  onclick="TableBuilder.selectWithUploaded('{{$field->getNameField()}}', 'multi_file', $(this) )">
                               {{__cms('Выбрать из загруженных')}}
                             </span>
                            <input type="hidden" name="{{$field->getNameField()}}" value='{{$field->getValue()}}'>
                            <input type="text"
                                   id="{{ $field->getNameField() }}"
                                   value=""
                                   placeholder="{{__cms('Выберите файлы для загрузки')}}"
                                   readonly="readonly">
                        </div>

                        @if ($field->getComment())
                            <div class="note">
                                {{$field->getComment()}}
                            </div>
                        @endif

                        <div class="tb-uploaded-file-container-{{$field->getNameField()}} uploaded-files">
                            <ul>
                                @if($field->getValue())
                                    @foreach($field->getValueArray() as $file)
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

                    @include('admin::form.fields.partials.select_files', ['isMultiple' => true])

                </div>


            </div>


        </div>
    </div>
</section>
