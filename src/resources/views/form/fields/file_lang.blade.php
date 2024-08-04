<section class="{{$field->getClassName()}}">
	<div class="tab-pane active">

		<ul class="nav nav-tabs tabs-pull-right">
			<label class="label pull-left" style="line-height: 32px;">{{$field->getName()}}</label>
			@foreach ($field->getLanguage() as $tab)
				<li class="{{$loop->first ? 'active' : ''}}">
					<a href="#{{$field->getNameFieldLangTab($definition, $tab)}}" class="tab_{{$tab->language}}" data-toggle="tab">{{$tab->language}}</a>
				</li>
			@endforeach
		</ul>

		<div class="tab-content padding-5">
			@foreach ($field->getLanguage() as $tab)
				<div class="tab-pane section_tab_{{$tab->language}} {{ $loop->first ? 'active' : '' }}" id="{{$field->getNameFieldLangTab($definition, $tab)}}">
					<div style="position: relative;">
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
										<span class="button select_with_uploaded"
											  data-name-model = "{{$definition->getFullPathDefinition()}}"
											  onclick="TableBuilder.selectWithUploaded('{{$field->getNameField()}}', 'one_file', $(this))"
											  style="right: 20px"
										>
									{{__cms('Выбрать из загруженных')}}
								 </span>
								 <input type="text"
									   id="{{ $field->getNameField() }}"
									   value="{{$field->getValueLanguage($tab->language)}}"
									   name="{{ $field->getNameField()}}[{{$tab->language}}]"
									   placeholder="{{$field->getValueLanguage($tab->language) ?: __cms('Выберите файл для загрузки')}}"
									   readonly="readonly">
							</div>

							@if ($field->getComment())
								<div class="note">
									{{$field->getComment()}}
								</div>
							@endif

							<div class="tb-uploaded-file-container-{{$field->getNameFieldLangTab($definition, $tab)}} tb-uploaded-file-container">
								@if ($field->getValueLanguage($tab->language))
									<a href="{{url($field->getValueLanguage($tab->language))}}" target="_blank">{{__cms('Скачать')}}</a> |
									<a class="delete" style="color:red;" onclick="$(this).parents('.files_type_fields').find('input[type=text]').val(''); $(this).parent().hide()">{{__cms('Удалить')}}</a>
								@endif
							</div>

							@include('admin::form.fields.partials.select_files', ['isMultiple' => false])

						</div>
					</div>
				</div>
			@endforeach

		</div>

	</div>


</section>

