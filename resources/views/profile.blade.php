<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('boonei/scaffold/js/app.js') }}" defer></script>


    <!-- Styles -->
    <link href="{{ asset('boonei/scaffold/css/app.css') }}" rel="stylesheet">
    <script>
        window._locale = '{{ app()->getLocale() }}';
        window._translations = {!! cache('scaffold.translations') !!};
    </script>
</head>
<body>
<div id="app" class="">

    <div class="flex flex-wrap">
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 bg-gray-800 p-4 text-white">
            hei
        </div>
        <div class="w-full sm:w-1/2 md:w-2/3 lg:w-3/4 bg-gray-100 p-4">
            <payment-method apitoken="{{ env('STRIPE_KEY') }}"></payment-method>
        </div>
    </div>

</div>
</body>
</html>




