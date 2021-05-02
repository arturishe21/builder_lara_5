@if(config("builder.translations.cms.languages"))
    <ul class="header-dropdown-list hidden-xs">
        <li>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span> {{config("builder.translations.cms.languages")[$thisLang] ?? ""}} </span> <i class="fa fa-angle-down"></i> </a>
            <ul class="dropdown-menu pull-right">

                @foreach(config("builder.translations.cms.languages") as $alias => $title)

                    <li {{$thisLang == $alias ? "class='active'" : ""}}>
                        <a href="{{route("change_lang"). "?lang=" .$alias}}">{{$title}}</a>
                    </li>
                @endforeach

            </ul>
        </li>
    </ul>
@endif
