@extends('admin::layouts.default')

@section('title')
  {{__cms('Переводы CMS')}}
@stop

@section('ribbon')
  <ol class="breadcrumb">
      <li><a href="/admin">{{__cms("Главная")}}</a></li>
      <li>{{__cms('Переводы CMS')}}</li>
  </ol>
@stop

@section('main')
 <div class="table_center_translate">
      @include("admin::translation_cms.part.center")
 </div>
@stop
