<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}} - {{ config('app.name') }}</title>
    <!-- Incluir CSS -->

    @include('partials.head')

</head>

<body class="fi-body fi-panel-admin min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white">

    <!-- Cabeçalho -->
    @include('partials.header')

    @include('partials.notification')

    <!-- Conteúdo da página -->
    <div class="container mx-auto mt-3">
        @yield('content')
    </div>

    <!-- Rodapé -->
    @include('partials.footer')
</body>
</html>
