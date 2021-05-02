<!-- HEADER -->
<header id="header">
    <div id="logo-group">
        <span id="logo" style="margin-top: 10px;">
           <img src="{{$admin->getLogo()}}">
        </span>
    </div>
    <!-- pulled right: nav area -->
    <div class="pull-right">

        <div id="hide-menu" class="btn-header pull-right">
            <span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fal fa-bars"></i></a> </span>
        </div>
        <div id="logout" class="btn-header transparent pull-right">
            <span> <a href="/admin/logout" title="{{__cms("Выход")}}" data-action="userLogout" ><i class="fal fa-sign-out"></i></a> </span>
        </div>
        @include('admin::partials.change_lang')
    </div>
</header>
