@extends('admin::layouts.default')

@section('title')
    {{__cms('Структура сайта')}}
@stop

@section('ribbon')
    <ol class="breadcrumb">
        <li><a href="/admin">{{__cms('Главная')}}</a></li>
        <li>{{__cms('Структура сайта')}}</li>
    </ol>
@stop

@section('main')
    @include('admin::tree.center')
@stop

