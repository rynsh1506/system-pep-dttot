<!DOCTYPE html>
<html lang="id" data-theme="goldenlight">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'System PEP & DTTOT' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body>
    {{ $slot }}

    @livewireScripts
</body>
</html>
