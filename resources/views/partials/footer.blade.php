<footer class="bottom-0 bg-gray-900 dark:bg-white w-full dark:text-white dark:text-gray-900 py-6">
    <div class="container mx-auto text-center">
        <p class="text-dark text-sm text-center">&copy; {{ date('Y') }} Lucas Pereira. Todos os direitos reservados.</p>
    </div>
</footer>


<!-- Dark:Mode -->
<script>
    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (document.documentElement.classList.contains('dark')) {
        var theme = 'dark';
    } else if (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        var theme = 'dark';
    } else {
        var theme = 'light';
    }

    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    // Change the icons inside the button based on previous settings
    if (theme === 'dark') {
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
    }

    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    var themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function() {
        // Toggle icons inside button
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // Toggle dark mode class on the <html> element
        document.documentElement.classList.toggle('dark');

        // Toggle the theme in localStorage
        var currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('color-theme', currentTheme);
    });
</script>

<script src="{{asset('js/fullcalendar-6.1.11/dist/index.global.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/fullcalendar-6.1.11/packages/core/locales/pt-br.global.js')}}" type="text/javascript"></script>

<script src="{{asset('js/filament/filament/app.js')}}"></script>
<script src="{{asset('js/filament/filament/echo.js')}}"></script>
<script src="{{asset('js/filament/support/support.js')}}"></script>
<script src="{{asset('js/filament/support/async-alpine.js')}}"></script>
<script src="{{asset('js/filament/notifications/notifications.js')}}"></script>

<!-- Livewire Scripts -->
<script src="{{asset('vendor/livewire/livewire.js')}}" data-csrf="{{ csrf_token() }}" data-update-uri="/livewire/update" data-navigate-once="true"></script>