<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('page-title') :: Admin</title>
    @include('layout.css')
</head>
<body data-ma-theme="green">
@php
    $adminData = Session::get('admin_login');
@endphp
@if (!Session::get('admin_login') && !$__env->yieldContent('login'))
    @php
        $backlink = urlencode(DIR_HTTP_CURRENT_PAGE);
    @endphp
    <script>
        var backlink = "{{$backlink}}";
        window.location.href = '/?backurl=' + backlink;
    </script>
@endif
<main class="main">
    <input type="hidden" id="csrf" value="{{csrf_token()}}">
    @if(Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            Success! {{Session::get('success')}}
        </div>
    @endif
    @if(Session::get('fail'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            Error! {{Session::get('fail')}}
        </div>
    @endif
{{--    <div class="page-loader">--}}
{{--        <div class="page-loader__spinner">--}}
{{--            <svg viewBox="25 25 50 50">--}}
{{--                <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>--}}
{{--            </svg>--}}
{{--        </div>--}}
{{--    </div>--}}
    @if (!$__env->yieldContent('login'))
        @include('layout.header')
    @endif
    @if (!$__env->yieldContent('login'))
        @include('layout.sidebar')
    @endif
    @if (!$__env->yieldContent('login'))
        <section class="content">
            @endif
            @if ($__env->yieldContent('page-header') || $__env->yieldContent('page-description'))
                <header class="content__title row justify-content-between align-items-center">
                    <div class="col-auto">
                        @if ($__env->yieldContent('page-header'))
                            <h1>@yield('page-header')</h1>
                        @endif
                        @if ($__env->yieldContent('page-description'))
                            <small>@yield('page-description')</small>
                        @endif
                    </div>
                    <div class="col-auto actions-div">
                        @if (!empty($actions))
                            {!! draw_action_buttons($actions) !!}
                        @endif
                        @if (isset($form_buttons) && $form_buttons === true)
                            {!! draw_form_buttons(array('save', 'save_back', 'reset'), $backlink) !!}
                        @endif
                    </div>
                </header>
            @endif
            <div class="row no-gutters">
                <div class="col-12">
                    @yield('page-content')
                </div>
            </div>
            @if (!$__env->yieldContent('login'))
                @include('layout.footer')
        </section>
    @endif
</main>

@include('layout.js')

<script src="{{DIR_HTTP_JS}}app.min.js"></script>
<script src="{{DIR_HTTP_JS}}MCQTable.js"></script>
<script src="{{DIR_HTTP_JS}}script.js"></script>
@yield('js')
</body>
</html>
