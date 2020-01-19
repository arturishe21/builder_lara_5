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
                        <label class="input">
                            <input type="text"
                                   value="{{$field->getValueLanguage($tab['postfix'])}}"
                                   name="{{ $field->getNameField() . $tab['postfix']}}"
                                   placeholder="{{{$tab['placeholder']}}}"
                                   class="dblclick-edit-input form-control input-sm unselectable">
                        </label>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
</section>


