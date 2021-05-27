@if (isset($permissions) && count($permissions))
    @foreach($permissions as $permissionAlias => $permission)

        @if (is_array($permission))
            <section style="border: 1px solid #ccc; padding: 10px">
                <p>{{__cms($permissionAlias)}}</p>
                @foreach($permission as $permissionSlug => $permissionTitle )
                    @if (is_array($permissionTitle))
                        <section style="padding-left: 10px;">
                            <p>{{__cms($permissionSlug)}}</p>
                            @foreach($permissionTitle as $permissionSlug2 => $permissionTitle2)

                                @if (is_array($permissionTitle2))
                                    <section style="padding-left: 10px;">
                                        <p>{{__cms($permissionSlug2)}}</p>
                                    @foreach($permissionTitle2 as $permissionLevel2Slug=>$permissionLevel2)

                                        <p>
                                            <label class="checkbox">
                                                <input type="checkbox" value="true" name="permissions[{{$permissionLevel2Slug}}]"
                                                   @if (isset($groupPermissionsThis[$permissionLevel2Slug]) && $groupPermissionsThis[$permissionLevel2Slug])
                                                   checked
                                                   @endif
                                                >
                                                <i></i> {{__cms($permissionLevel2)}}
                                            </label>
                                        </p>
                                    @endforeach
                                    </section>

                                @else

                                    <p><label class="checkbox">
                                            <input type="checkbox" value="true" name="permissions[{{$permissionSlug2}}]"
                                                   @if (isset($groupPermissionsThis[$permissionSlug2]) && $groupPermissionsThis[$permissionSlug2])
                                                   checked
                                                @endif
                                            >
                                            <i></i> {{__cms($permissionTitle2)}}
                                        </label>
                                    </p>
                                @endif
                            @endforeach
                        </section>
                    @else

                        <p><label class="checkbox">
                                <input type="checkbox" value="true" name="permissions[{{$permissionSlug}}]"
                                       @if (isset($groupPermissionsThis[$permissionSlug]) && $groupPermissionsThis[$permissionSlug])
                                       checked
                                    @endif
                                >
                                <i></i> {{__cms($permissionTitle)}}
                            </label>
                        </p>

                    @endif
                @endforeach
            </section>
        @else
            <section>
                <p><label class="checkbox">
                        <input type="checkbox" value="true" name="permissions[{{$permissionAlias}}]"
                               @if (isset($groupPermissionsThis[$permissionAlias]) && $groupPermissionsThis[$permissionAlias])
                               checked
                            @endif
                        >
                        <i></i> {{__cms($permission)}}
                    </label>
                </p>
            </section>
        @endif

    @endforeach
@endif
