<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
    @if(View::hasSection('title'))
        {{ config('app.name', 'Laravel') }} &raquo; @yield('title')
    @else
		{{ config('app.name', 'Laravel') }}
    @endif
  </title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="{{asset('js/admin-lte3.js')}}"></script>
  <link rel="stylesheet" href="{{asset('css/admin-lte3.css')}}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <!-- Google Font -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
@stack('scripts')
</head>
<body class="hold-transition  login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>{{ config('app.name', 'Laravel') }}</b></a>
  </div>
	@yield('content')
</div>
</body>
</html>
