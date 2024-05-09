<div>
    <header class="fi-simple-header py-8">
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            Meu Perfil
        </h1>
        <p class="fi-simple-header-subheading mt-2 text-left text-gray-500 dark:text-gray-400">
            Gerencie seu perfil de usuário aqui
        </p>
    </header>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
    </x-filament-panels::form>
</div>