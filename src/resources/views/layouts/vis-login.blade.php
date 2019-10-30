
<!DOCTYPE html>
<html lang="en-us">

    <head>
        <meta charset="utf-8">
        <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

        <title>{{ __cms(config('builder.admin.caption')) }}</title>

        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <link rel="stylesheet" type="text/css" href="/packages/vis/builder/css/all_login.css">


        <link rel="shortcut icon" href="{{ config('builder.admin.favicon_url') }}" type="image/x-icon">
        <link rel="icon" href="{{ config('builder.admin.favicon_url') }}" type="image/x-icon">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

        @if (config('builder.login.css'))
            <link rel="stylesheet" type="text/css" href="{{config('builder.login.css')}}">
        @endif

    </head>
    <body id="login" class="animated fadeInDown">
        @yield('main')
        <script src="/packages/vis/builder/js/all_login.js"></script>
    </body>

</html>

