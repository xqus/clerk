<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('boonei/scaffold/js/app.js') }}" defer></script>
    <script>
        window._locale = '{{ app()->getLocale() }}';
        window._translations = {!! cache('scaffold.translations') !!};
    </script>
    <link href="{{ asset('boonei/scaffold/css/app.css') }}" rel="stylesheet">
</head>

<body>
<div id="app" class="">

    <div class="flex flex-wrap">
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 bg-gray-800 p-4 text-white text-center sm:pt-64">
            <p class="text-xl">{{__('Authenticated as')}}</p>
            <p class="text-2xl">{{ $user = Auth::user()->name }}</p>
        </div>
        <div class="h-screen w-full sm:w-1/2 md:w-2/3 lg:w-3/4 bg-gray-100 p-4">
            <payment-method apitoken="{{ env('STRIPE_KEY') }}"></payment-method>
        </div>
    </div>

</div>
</body>
</html>




