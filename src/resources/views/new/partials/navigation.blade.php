
<aside id="left-panel">
    <div class="login-info">
        <span>
            <a>
                <img src="" class="online">
                <span>
                    {{$user->getFullName()}}
                </span>
            </a>
        </span>
    </div>
    <!-- end user info -->
    <nav>
        <ul style="display: block;">
            @foreach($menu as $k=>$el)
                @if(app('user')->hasAccess([str_replace(['/', '_'], [''], $el['link']).'.view']))

                    <li class="level1">
                        <a  {!! isset($el['link']) && !isset($el['submenu'])? "href='/admin".$el['link']."'" : "" !!}>
                            @if (isset($el['icon']))
                                <i class="fa fa-lg fa-fw fa-{{$el['icon']}}"></i>
                            @endif
                            @if (isset($el['title']))
                                <span class="menu-item-parent">{{__cms($el['title'])}}</span>
                            @endif
                            @if (isset($el['badge']))
                                <?php $badgeValue = $el['badge'](); ?>
                                <span
                                    class="badge bg-color-greenLight inbox-badge"
                                    style="@if(!$badgeValue) display: none @endif"
                                >
                                        {{is_numeric($badgeValue) ? $badgeValue : ''}}
                                    </span>
                            @endif
                        </a>

                        @if(isset($el['submenu']))
                            <ul>
                                @foreach($el['submenu'] as $k_sub_menu=>$sub_menu)
                                    @if(app('user')->hasAccess([str_replace(['/', '_'], [''], $sub_menu['link']).'.view']))
                                        <li>
                                            <a
                                                {!! isset($sub_menu['link']) && !isset($sub_menu['submenu']) ? "href='/admin".$sub_menu['link']."'" : "" !!}
                                            >{{__cms($sub_menu['title'])}}

                                                @if (isset($sub_menu['badge']))
                                                    <?php $badgeValue = $sub_menu['badge'](); ?>

                                                    <span
                                                        class="badge bg-color-greenLight  inbox-badge"
                                                        style="@if(!$badgeValue) display: none @endif"
                                                    >
                                                        {{is_numeric($badgeValue) ? $badgeValue : ''}}
                                                    </span>
                                                @endif
                                            </a>
                                            @if(isset($sub_menu['submenu']))

                                                <ul>
                                                    @foreach($sub_menu['submenu'] as $k_sub_menu2=>$sub_menu2)

                                                        @if(app('user')->hasAccess([str_replace(['/', '_'], [''], $sub_menu2['link']).'.view']))
                                                            <li
                                                                @if (isset($sub_menu2['badge']))
                                                                style="align-items: center;justify-content: space-between;display: flex;"
                                                                @endif>
                                                                <a {!!isset($sub_menu2['link']) && !isset($sub_menu2['submenu']) ? "href='/admin".$sub_menu2['link']."'" : "" !!}>{{__cms($sub_menu2['title'])}}</a>

                                                                @if (isset($sub_menu2['badge']))
                                                                    <?php $countBadge = $sub_menu2['badge'](); ?>
                                                                    @if (is_numeric($countBadge))
                                                                        <span class="badge bg-color-greenLight inbox-badge">{{$countBadge}}</span>
                                                                    @endif
                                                                @endif
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
    <span class="minifyme" data-action="minifyMenu">
        <i class="fa fa-arrow-circle-left hit"></i>
    </span>
</aside>
<script>
    //check empty folder
    $( "#left-panel .level1 ul" ).each(function( index ) {
        if ($.trim($(this).html()) == '') {
            $(this).parent().hide();
        }
    });
</script>
