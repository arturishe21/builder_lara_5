<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title> @yield('title') - {{ __cms(config('builder.admin.caption')) }}</title>
    <meta name="description" content="">
    <meta name="author" content="VIS-A-VIS">
    <meta name="HandheldFriendly" content="True">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="{{ config('builder.admin.favicon_url') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/packages/vis/builder/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    <script src="/packages/vis/builder/js/all_header1.js"></script>

    @yield('styles')
    @yield('scripts_header')

    <script src="/packages/vis/builder/js/all_header2.js"></script>

    @if ($admin->getJs())
        @foreach($admin->getJs() as $jsFile)
            <script src="{{$jsFile}}"></script>
        @endforeach
    @endif

    @if ($admin->getCss())
        @foreach($admin->getCss() as $cssFile)
            <link type="text/css" rel="stylesheet" href="{{$cssFile}}" />
        @endforeach
    @endif

    <script type="text/javascript" src="/packages/vis/builder/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/packages/vis/builder/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.js"></script>
    <style>
        .smart-style-4 .tb-pagination nav>ul>li>a {
            padding:6px 12px;
        }
    </style>
    <link rel="shortcut icon" href="/packages/vis/builder/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/packages/vis/builder/img/favicon/favicon.ico" type="image/x-icon">

</head>
<body class="{{ Cookie::get('tb-misc-body_class', '') }} {{ $skin ?? '' }}">
<div id="modal_wrapper" class="modal_popup_first"></div>
<div class="table_form_create modal_popup_first"></div>
<div class="foreign_popups"></div>
@include('admin::new.partials.header')
@include('admin::new.partials.navigation')
<div id="main" role="main">
    <div id="main-content">
        <div id="ribbon">
            @yield('ribbon')
        </div>
        <div id="content">
            @yield('headline')
            <div id="content">
                <div class="row" id="content_admin">

                    @yield('main')

                </div>
            </div>
        </div>
    </div>
</div>
@include('admin::new.partials.scripts')
@yield('scripts')
@include('admin::new.partials.translate_phrases')

<div class="load_page" style="position: fixed; display: none; opacity: 0.7; z-index: 1111111; height: 50px; top: 10px; right: 30px"><i class="fa fa-spinner fa-spin" style="font-size: 40px"></i></div>
@include('admin::new.partials.popup_cropp')

<script src="/packages/vis/builder/js/cropper_model.js"></script>
</body>
</html>
