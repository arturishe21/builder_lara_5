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
                    <div style="position: relative;" >
                        <div class="no_active_froala">
                             <textarea class="text_block" name="{{ $field->getNameField()}}[{{$tab->language}}]"
                                   toolbar = "{{$field->getToolbar()}}"
                                   inlineStyles = ''
                                   options = '{{ $field->getOptions()}}'>{{$field->getValueLanguage($tab->language)}}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
</section>
