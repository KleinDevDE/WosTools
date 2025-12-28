<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Puzzle Trading</title>

    <meta name="theme-color" content="#0a0f1c">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    @vite(['resources/assets/css/app.css', 'resources/assets/js/app.js'], 'build-puzzles')
</head>
<body class="antialiased">
    <div id="app"></div>
</body>
</html>
