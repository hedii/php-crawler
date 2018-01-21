<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script>
      window.apiUrl = '{{ route('api.index') }}';
      window.apiRoutes = @json(routes_to_array('api'));
    </script>
</head>
<body>
<div id="app">
    @include('partials.header')
    @include('partials.main')
    @include('partials.footer')
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
