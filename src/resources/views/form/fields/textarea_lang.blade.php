<section class="{{$field->getClassName()}}">
    <div class="tab-pane active">

        <ul class="nav nav-tabs tabs-pull-right">
            <label class="label pull-left" style="line-height: 32px;">{{$field->getName()}}</label>
            @foreach ($field->getLanguage() as $tab)
                <li class="{{$loop->first ? 'active' : ''}}">
                    <a href="#{{$field->getNameFieldLangTab($definition, $tab)}}" data-toggle="tab">{{$tab['caption']}}</a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content padding-5">
            @foreach ($field->getLanguage() as $tab)
                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{$field->getNameFieldLangTab($definition, $tab)}}">
                    <div style="position: relative;">
                        <label class="textarea">
                            <textarea rows="{{$rows ?? '3'}}"
                              class="custom-scroll"
                              id="{{ $field->getNameField() . $tab['caption']}}"
                              name="{{ $field->getNameField()}}[{{$tab['caption']}}]">{{$field->getValueLanguage($tab['caption'])}}</textarea>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
