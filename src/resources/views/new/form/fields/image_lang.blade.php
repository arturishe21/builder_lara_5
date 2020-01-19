<section class="section_field">
    <div class="tab-pane active">

        <ul class="nav nav-tabs tabs-pull-right">
            <label class="label pull-left" style="line-height: 32px;">{{$field->getName()}}</label>
            @foreach ($field->getLanguage() as $tab)
                <li class="{{$loop->first ? 'active' : ''}}">
                    <a href="#{{$field->getNameField() . $tab['postfix']}}" data-toggle="tab">{{__cms($tab['caption'])}}</a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content padding-5">
            @foreach ($field->getLanguage() as $tab)
                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{$field->getNameField() . $tab['postfix']}}">
                    <div style="position: relative;">
                        <div class="pictures_input_field">
                            <div class="picture_block">
                                <div class="progress progress-micro" style="margin-bottom: 0;">
                                    <div class="img-progress progress-bar progress-bar-primary bg-color-redLight" role="progressbar"
                                         style="width: 0%;"></div>
                                </div>
                                <div class="input input-file">
                                    <span class="button select_with_uploaded" onclick="TableBuilder.selectWithUploadedImages('{{$field->getNameField()}}', 'one_file', $(this), '{{$field->getNameField() . $tab['postfix']}}', '{{request('id_tree')}}')"> {{__cms('Выбрать из загруженных')}}</span>
                                    <span class="button">
                                <input type="file" accept="image/*" onchange="TableBuilder.uploadImage(this, '{{$field->getNameField()}}', '{{$field->getNameField() . $tab['postfix']}}');">
                                        {{__cms('Загрузить')}}
                            </span>
                                    <input type="text" id="{{$field->getNameField() . $tab['postfix']}}" placeholder="{{__cms('Выберите изображение для загрузки')}}" readonly="readonly">
                                    <input type="hidden" value="{{$field->getValueLanguage($tab['postfix'])}}" name="{{ $field->getNameField() . $tab['postfix'] }}">
                                </div>
                                <div class="tb-uploaded-image-container image-container_{{ $field->getNameField() . $tab['postfix'] }}">


                                    @if ($field->getValueLanguage($tab['postfix']))
                                        <div class="{{$field->isTransparent() ? 'transparent-image' : ''}}" style="position: relative; display: inline-block;" >
                                            <img class="image-attr-editable"
                                                 data-tbident="{{$field->getNameField() . $tab['postfix']}}"
                                                 @if (strpos($field->getValueLanguage($tab['postfix']), ".svg"))
                                                 width="200"
                                                 src="/{{ $field->getValueLanguage($tab['postfix'])}}" src_original="{{$field->getValueLanguage($tab['postfix'])}}"
                                                 @else
                                                 src="{{ glide($field->getValueLanguage($tab['postfix']), ['w' => 200, 'h' => 200]) }}"
                                                 src_original="{{$field->getValueLanguage($tab['postfix'])}}"
                                                 @endif

                                                 style="max-width: 200px"
                                            />
                                            <div class="tb-btn-delete-wrap">
                                                <button class="btn btn-default btn-sm tb-btn-image-delete"
                                                        type="button"
                                                        onclick="TableBuilder.deleteSingleImage('{{$field->getNameField() . $tab['postfix']}}', this);">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <p style="padding: 20px 0 10px 0">{{__cms('Нет изображения')}}</p>
                                    @endif


                                </div>
                            </div>
                            <div class="modal files_uploaded_table" id ='files_uploaded_table_{{ $field->getNameField()}}' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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
                                            <span class="btn btn-success btn-sm" onclick="TableBuilder.selectImageUploaded('{{ $field->getNameField()}}', 'once')" >{{__cms('Выбрать')}}</span>
                                            <span class="btn btn-default"  onclick="TableBuilder.closeWindowWithPictures();"> {{__cms('Отмена')}} </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>


</section>


