@extends('admin::new.layouts.default')

@section('title')
  {{__cms($title)}}
@stop

@section('ribbon')
  <ol class="breadcrumb">
      <li><a href="/admin">{{__cms("Главная")}}</a></li>
      @foreach($breadcrumb as $k=>$el)
            <li><a href="{{$el}}"></a>{{__cms($k)}}</li>
      @endforeach
  </ol>
@stop

@section('main')
 <div class="table_center_translate">
      @include("admin::translation_cms.part.translate_cms_center")
 </div>
@stop
