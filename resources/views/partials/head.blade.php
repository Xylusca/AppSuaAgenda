<style>
    [x-cloak=''],
    [x-cloak='x-cloak'],
    [x-cloak='1'] {
        display: none !important;
    }

    @media (max-width: 1023px) {
        [x-cloak='-lg'] {
            display: none !important;
        }
    }

    @media (min-width: 1024px) {
        [x-cloak='lg'] {
            display: none !important;
        }
    }
</style>


<link href="{{asset('css/filament/forms/forms.css')}}" rel="stylesheet" />
<link href="{{asset('css/filament/support/support.css')}}" rel="stylesheet" />

<style>
    :root {
        --danger-50: 255, 241, 242;
        --danger-100: 255, 228, 230;
        --danger-200: 254, 205, 211;
        --danger-300: 253, 164, 175;
        --danger-400: 251, 113, 133;
        --danger-500: 244, 63, 94;
        --danger-600: 225, 29, 72;
        --danger-700: 190, 18, 60;
        --danger-800: 159, 18, 57;
        --danger-900: 136, 19, 55;
        --danger-950: 76, 5, 25;
        --gray-50: 249, 250, 251;
        --gray-100: 243, 244, 246;
        --gray-200: 229, 231, 235;
        --gray-300: 209, 213, 219;
        --gray-400: 156, 163, 175;
        --gray-500: 107, 114, 128;
        --gray-600: 75, 85, 99;
        --gray-700: 55, 65, 81;
        --gray-800: 31, 41, 55;
        --gray-900: 17, 24, 39;
        --gray-950: 3, 7, 18;
        --info-50: 239, 246, 255;
        --info-100: 219, 234, 254;
        --info-200: 191, 219, 254;
        --info-300: 147, 197, 253;
        --info-400: 96, 165, 250;
        --info-500: 59, 130, 246;
        --info-600: 37, 99, 235;
        --info-700: 29, 78, 216;
        --info-800: 30, 64, 175;
        --info-900: 30, 58, 138;
        --info-950: 23, 37, 84;
        --primary-50: 249, 250, 247;
        --primary-100: 242, 244, 239;
        --primary-200: 223, 228, 216;
        --primary-300: 203, 211, 192;
        --primary-400: 164, 178, 144;
        --primary-500: 125, 145, 97;
        --primary-600: 113, 131, 87;
        --primary-700: 94, 109, 73;
        --primary-800: 75, 87, 58;
        --primary-900: 61, 71, 48;
        --primary-950: 38, 44, 29;
        --success-50: 236, 253, 245;
        --success-100: 209, 250, 229;
        --success-200: 167, 243, 208;
        --success-300: 110, 231, 183;
        --success-400: 52, 211, 153;
        --success-500: 16, 185, 129;
        --success-600: 5, 150, 105;
        --success-700: 4, 120, 87;
        --success-800: 6, 95, 70;
        --success-900: 6, 78, 59;
        --success-950: 2, 44, 34;
        --warning-50: 255, 247, 237;
        --warning-100: 255, 237, 213;
        --warning-200: 254, 215, 170;
        --warning-300: 253, 186, 116;
        --warning-400: 251, 146, 60;
        --warning-500: 249, 115, 22;
        --warning-600: 234, 88, 12;
        --warning-700: 194, 65, 12;
        --warning-800: 154, 52, 18;
        --warning-900: 124, 45, 18;
        --warning-950: 67, 20, 7;
    }
</style>

<link href="{{asset('css/filament/filament/app.css')}}" rel="stylesheet" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="{{ asset('css/fonts.bunny.css') }}" rel="stylesheet" />

<style>
    :root {
        --font-family: 'Inter';
        --sidebar-width: 20rem;
        --collapsed-sidebar-width: 4.5rem;
        --default-theme-mode: system;
    }

    .fc-toolbar-title {
        text-transform: capitalize;
    }
</style>

<!-- Livewire Styles -->
<style>
    [wire\:loading][wire\:loading],
    [wire\:loading\.delay][wire\:loading\.delay],
    [wire\:loading\.inline-block][wire\:loading\.inline-block],
    [wire\:loading\.inline][wire\:loading\.inline],
    [wire\:loading\.block][wire\:loading\.block],
    [wire\:loading\.flex][wire\:loading\.flex],
    [wire\:loading\.table][wire\:loading\.table],
    [wire\:loading\.grid][wire\:loading\.grid],
    [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {
        display: none;
    }

    [wire\:loading\.delay\.none][wire\:loading\.delay\.none],
    [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest],
    [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter],
    [wire\:loading\.delay\.short][wire\:loading\.delay\.short],
    [wire\:loading\.delay\.default][wire\:loading\.delay\.default],
    [wire\:loading\.delay\.long][wire\:loading\.delay\.long],
    [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer],
    [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {
        display: none;
    }

    [wire\:offline][wire\:offline] {
        display: none;
    }

    [wire\:dirty]:not(textarea):not(input):not(select) {
        display: none;
    }

    :root {
        --livewire-progress-bar-color: #2299dd;
    }

    [x-cloak] {
        display: none !important;
    }
</style>

<script src="{{ asset('js/jquery-3.7.1.js') }}"></script>

<link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>