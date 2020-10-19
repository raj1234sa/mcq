@extends('layout.main')
@section('login', 'yes')
@section('page-title', 'Login')
@section('page-content')
    <div class="login">
        <div class="login__block active" id="l-login">
            <div class="login__block__body">
                @if(Session::get('fail'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        Oh snap! {{Session::get('fail')}}
                    </div>
                @endif
                <form action="/login-auth" method="post">
                    @csrf
                    <input type="text" class="d-none" name="backurl" value="{{$backurl}}">
                    <div class="form-group form-group--float form-group--centered">
                        <input type="text" class="form-control" name="username">
                        <label>Username</label>
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group form-group--float form-group--centered">
                        <input type="password" class="form-control" name="password" id="password">
                        <label>Password</label>
                        <i class="form-group__bar"></i>
                    </div>

                    <button type="submit" class="btn btn--icon login__block__btn"><i
                            class="zmdi zmdi-long-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{DIR_HTTP_JS}}md5.js"></script>
    <script>
        $(document).ready(function () {
            $("button[type=submit]").click(function (e) {
                e.preventDefault();
                $("#password").val($.md5($("#password").val()));
                $("form").submit();
            });
        });
    </script>
@endsection
