<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nando Cortes - Barbearia de Excelência</title>
    <meta name="description" content="Bem-vindo à Nando Cortes, a barbearia onde estilo e precisão se encontram. Oferecemos cortes de cabelo personalizados e serviços de barbearia de alta qualidade em um ambiente moderno e acolhedor. Venha nos visitar e transforme seu visual!">

    <!-- Incluir CSS -->
    @include('partials.head')

</head>

<body class="fi-body fi-panel-admin min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white">
    <div id="load" class="hidden fixed top-0 left-0 h-screen w-screen bg-black bg-opacity-50 flex items-center justify-center z-50">
        <x-filament::loading-indicator class="h-5 w-5 absolute top-1/2 left-1-2 inset-0" />
    </div>
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
