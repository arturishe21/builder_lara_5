@extends('admin::layouts.login')

@section('main')
    <div id="main" role="main">
        <div id="content" class="container">
                <div class="b-login col-xs-12 col-sm-12 col-md-5 col-lg-4">
                    <div class="well no-padding">

                        @if (Session::has('login_not_found'))
                            <div class="alert alert-danger fade in">
                                {{Session::get('login_not_found')}}
                            </div>
                        @endif

                        <form method="post" action="{{route('cms.login.store')}}" name="repawning" id="login-form" class="smart-form client-form">
                            {{ csrf_field() }}

                            <fieldset>
                                <section>
                                    <label class="label">Email</label>
                                    <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input type="email" name="email" email_required = "{{__cms('Введите адрес эл.почты')}}" email_email = "{{__cms('Введите валидный адрес эл.почты')}}">
                                       </label>
                                </section>
                                <section>
                                    <label class="label">{{__cms('Пароль')}}</label>
                                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                                        <input type="password" name="password" password_required = "{{__cms('Введите пароль')}}" autocomplete="off">
                                        </label>
                                </section>
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary submit_button">
                                    {{__cms('Войти')}}
                                </button>
                            </footer>
                      </form>
                    </div>
                </div>
        </div>
    </div>
@stop

