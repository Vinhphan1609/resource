@extends('auth.layout.default')
@section('content')
@section('pageTitle', 'Login')

<div id="content" class="col-md-12 full-page login">
    <div class="inside-block">
        <img src="{{asset('images/chainos.png')}}" alt class="logo" style="transform: scale(.75);">
        <h1><strong>Welcome</strong> SmartKiot</h1>
        <h5>Chainos Solution</h5>
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible ol-md-11 col-sm-11" role="alert">
                {{Session::get('success')}}
            </div>
        @endif
        @if(Session::has('error') && !empty(Session::has('error')))

            <div class="alert alert-danger" style="margin-top: 50px;" role="alert">
                {{Session::get('error')}}
            </div>
        @endif

        <form id="form-signin" class="form-signin" action=" {{url('doLogin')}} " method="post">
            {{ csrf_field() }}
            <section>
                <div class="input-group">
                    <input type="text" class="form-control" name="name" placeholder="Username">
                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                </div>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <div class="input-group-addon"><i class="fa fa-key"></i></div>
                </div>
            </section>

            <section class="controls">
                <a href="/reset-password">Forget password?</a>
            </section>
            <section class="log-in">
                <button class="btn btn-greensea" type="submit">Log In</button>
                <span>or</span>
                <a class="btn btn-primary" href="/register">Create an account</a>
            </section>
        </form>
    </div>
</div>
@endsection