@extends('admin::layouts.default')

@section('title')
  {{__cms('Переводы')}}
@stop

@section('ribbon')
  <ol class="breadcrumb">
      <li><a href="/admin">{{__cms('Главная')}}</a></li>
     <li><a>{{__cms('Переводы')}}</a></li>
  </ol>
@stop


@section('main')
   @include("admin::translations.part.table_center")
@stop
