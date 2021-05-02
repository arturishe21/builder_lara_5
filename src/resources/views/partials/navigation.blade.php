<aside id="left-panel">
    <div class="login-info">
        <span>
            <a>
                <span>
                    {{$user->getFullName()}}
                </span>
            </a>
        </span>
    </div>
    <!-- end user info -->
    <nav>
        <ul style="display: block;">
            @foreach($menu as $menuItemLevel0)
                @if(app('user')->hasAccessForCms($menuItemLevel0['link']))

                    <li class="level1">
                        <a
                            @if (isset($menuItemLevel0['link']) && !isset($menuItemLevel0['submenu']))
                            href="/admin{{$menuItemLevel0['link']}}"
                            @endif
                        >
                            @if (isset($menuItemLevel0['icon']))
                                <i class="fal fa-lg fa-fw fa-{{$menuItemLevel0['icon']}}"></i>
                            @endif

                            <span class="menu-item-parent">{{__cms($menuItemLevel0['title'])}}</span>

                            @include('admin::partials.navigation_badge', ['menu' => $menuItemLevel0])
                        </a>

                        @if(isset($menuItemLevel0['submenu']))
                            <ul>
                                @foreach($menuItemLevel0['submenu'] as $subMenu)
                                    @if(app('user')->hasAccessForCms($subMenu['link']))

                                        <li>
                                            <a
                                                @if (isset($subMenu['link']) && !isset($subMenu['submenu']))
                                                href='/admin{{$subMenu['link']}}'
                                                @endif
                                            >{{__cms($subMenu['title'])}}

                                                @include('admin::partials.navigation_badge', ['menu' => $subMenu])
                                            </a>
                                            @if(isset($subMenu['submenu']))
                                                <ul>
                                                    @foreach($subMenu['submenu'] as $subMenu2)

                                                        @if(app('user')->hasAccessForCms($subMenu2['link']))
                                                            <li
                                                                @if (isset($subMenu2['badge']))
                                                                style="align-items: center;justify-content: space-between;display: flex;"
                                                                @endif>
                                                                <a {!!isset($subMenu2['link']) && !isset($subMenu2['submenu']) ? "href='/admin".$subMenu2['link']."'" : "" !!}>{{__cms($subMenu2['title'])}}</a>

                                                                @include('admin::partials.navigation_badge', ['menu' => $subMenu2])
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
