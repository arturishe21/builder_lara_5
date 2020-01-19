@extends('admin::new.layouts.default')

@section('title')
      {{__cms($title)}}
@stop
@section('ribbon')
      <ol class="breadcrumb">
          <li><a href="/admin">{{__cms('Главная')}}</a></li>
          @foreach($breadcrumb as $k=>$el)
                <li><a href="{{$el}}"></a>{{__cms($k)}}</li>
          @endforeach
      </ol>
@stop
@section('main')
     <div>
        @include("admin::settings.part.settings_center")
     </div>
@stop
