@extends('admin::layouts.default')

@section('title')
    Логи сайта
@stop

@section('ribbon')
    <ol class="breadcrumb">
        <li><a href="/admin"> {{__cms('Главная')}}</a></li>
        <li>Логи сайта</li>
    </ol>
@stop

@section('main')
    @include('admin::logs.center')
@stop
