<div class="modal-body">
    <form id="{{$type}}_form_{{$definition->getNameDefinition()}}" class="smart-form" method="post" action="{{$definition->getUrlAction()}}" novalidate="novalidate">

        @if (isset($fields[0]))
            <fieldset style="{{ request('edit') ? '' : 'padding:0;' }}">

                @include('admin::form.fields_simple', ['fields' => $fields])

                @if (request()->id)
                    <input type="hidden" name="id" value="{{ request()->id }}" />
                @endif
            </fieldset>

        @else
            <ul class="nav nav-tabs bordered">
                @foreach ($fields as $title => $field)
                    <li @if ($loop->first) class="active" @endif><a href="#tabform{{$definition->getNameDefinition()}}-{{$loop->index}}" data-toggle="tab">{{ __cms($title) }}</a></li>
                @endforeach
            </ul>
            <div class="tab-content padding-10">
                @foreach ($fields as $title => $fieldsBlock)
                    <div class="tab-pane @if ($loop->first) active @endif" id="tabform{{$definition->getNameDefinition()}}-{{$loop->index}}">
                        <div class="table-responsive">
                            <fieldset style="padding:0">

                                @include('admin::form.fields_simple', ['fields' => $fieldsBlock])

                                @if (request()->id)
                                    <input type="hidden" name="id" value="{{ request()->id }}" />
                                @endif
                            </fieldset>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if (request('foreign_attributes'))
            <input type="hidden" name="foreign_attributes" value="{{request('foreign_attributes')}}">
        @endif

    </form>
</div>

<div class="modal-footer">
    @if (app('user')->hasAccessActionsForCms('save'))
    <button onclick="$('#{{$type}}_form_{{$definition->getNameDefinition()}}').submit();" type="button" class="btn btn-success btn-sm">
        <span class="glyphicon glyphicon-floppy-disk"></span> {{__cms('Сохранить')}}
    </button>
    @endif
    <button type="button" class="btn btn-default close_button" onclick="TableBuilder.doClosePopup('{{$definition->model()->getTable()}}')">
        {{__cms('Отмена')}}
    </button>
</div>
@include('admin::tb.form_js')
