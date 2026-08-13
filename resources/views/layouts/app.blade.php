<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="icon" href="/assets/logo-inovindo.webp" type="image/webp" sizes="32x32">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        @wirekitStyles
</head>
    <body>
        {{ $slot }}

        @wirekitScripts
        @livewireScripts
    </body>
</html>
