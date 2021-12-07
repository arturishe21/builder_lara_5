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
                        <label class="textarea">
                            <textarea rows="{{$rows ?? '3'}}"
                              class="custom-scroll"
                              id="{{ $field->getNameField() . $tab->language}}"
                              name="{{ $field->getNameField()}}[{{$tab->language}}]">{{$field->getValueLanguage($tab->language)}}</textarea>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

