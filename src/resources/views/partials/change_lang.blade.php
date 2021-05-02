<ul class="header-dropdown-list hidden-xs">
    <li>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span> {{$languages[$thisLang] ?? ""}} </span> <i class="fa fa-angle-down"></i> </a>
        <ul class="dropdown-menu pull-right">
            @foreach($languages as $slugLanguage => $titleLanguage)
                <li {{$thisLang == $slugLanguage ? "class='active'" : ""}}>
                    <a href="{{route("change_lang"). "?lang=" .$slugLanguage}}">{{$titleLanguage}}</a>
                </li>
            @endforeach
        </ul>
    </li>
</ul>

