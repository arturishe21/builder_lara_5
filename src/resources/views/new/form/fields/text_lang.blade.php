<section class="{{$field->getClassName()}}">
    <div class="tab-pane active">

        <ul class="nav nav-tabs tabs-pull-right">
            <label class="label pull-left" style="line-height: 32px;">{{$field->getName()}}</label>
            @foreach ($field->getLanguage() as $tab)
                <li class="{{$loop->first ? 'active' : ''}}">
                    <a href="#{{$definition->getNameDefinition() . $field->getNameField() . $tab['postfix']}}" data-toggle="tab">{{__cms($tab['caption'])}}</a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content padding-5">
            @foreach ($field->getLanguage() as $tab)
                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{$definition->getNameDefinition() . $field->getNameField() . $tab['postfix']}}">
                    <div style="position: relative;">
                        <label class="input">
                            <input type="text"
                                   value="{{$field->getValueLanguage($tab['postfix'])}}"
                                   name="{{ $field->getNameField() . $tab['postfix']}}"
                                   placeholder="{{{$tab['placeholder']}}}"
                                   class="dblclick-edit-input form-control input-sm unselectable"
                                   data-name-input="{{$definition->getNameDefinition().$field->getNameField(). $tab['postfix']}}"
                            >
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@include('admin::new.form.fields.partials.traslation')

