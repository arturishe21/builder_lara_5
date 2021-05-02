<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <title>{{ $admin->getCaption()}}</title>
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="/packages/vis/builder/css/all_login.css">
        <link rel="shortcut icon" href="{{ $admin->getFaviconUrl() }}" type="image/x-icon">
        <link rel="icon" href="{{ $admin->getFaviconUrl() }}" type="image/x-icon">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

        @if ($login->getCss())
            <link rel="stylesheet" type="text/css" href="{{$login->getCss()}}">
        @endif

    </head>
    <body id="login" class="animated fadeInDown">
        @yield('main')
        <script src="/packages/vis/builder/js/all_login.js"></script>
    </body>

</html>

