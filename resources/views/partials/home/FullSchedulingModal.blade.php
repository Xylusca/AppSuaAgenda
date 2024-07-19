<style>
    .bg-con {
        background-color: rgb(113 131 87);
    }

    .bg-can {
        background-color: rgb(211 64 64);
    }
</style>
<!-- Modal -->
<x-filament::modal width="4xl" dark id="myModal">
    <!-- button Cart  -->
    <x-slot name="trigger">
        <x-filament::button icon="heroicon-s-shopping-cart" class="btn-fixed border">
            <span id="cart-count" class="hidden m-o p-0"></span>
        </x-filament::button>
    </x-slot>

    <x-slot name="heading">
        <x-filament::button icon="heroicon-m-arrow-left-circle" id="btn_back" class="flex">
            <span>Voltar</span>
        </x-filament::button>
        <x-filament::button icon="heroicon-o-magnifying-glass-circle" id="btn-check_appointment" class="flex" @click="renderModalContent(0)">
            <span>Consultar Agendamento</span>
        </x-filament::button>
    </x-slot>

    <!-- Check Appointment -->
    <div class="bg-white dark:bg-gray-900 dark:text-white p-8 hidden" id="check_appointment">
        <div class="bkfd text-center p-3 rounded mb-3">
            <h2 class="text-center font-bold text-xl mb-2">Consultar Agendamento</h2>
            <p class="text-sm">Para consultar seu agendamento, por favor, preencha o campo abaixo com seu número de telefone, e clique no botão ao lado.</p>
        </div>
        <div class="flex items-center space-x-2">
            <x-filament::input.wrapper class="w-full">
                <x-filament::input type="text" placeholder="(DD) 99999-9999" id="check_appointment_whatsapp" name="whatsapp" required="required" wire:model="check_appointment_whatsapp" x-mask="(99) 99999-9999" />
            </x-filament::input.wrapper>

            <x-filament::button icon="heroicon-s-magnifying-glass" id="btn-check_appointment" class="flex ml-btn" @click="checkAppointment()">
            </x-filament::button>
        </div>

        <div class="mt-3 rounded">

            <table id="schedulingTable" class="border-collapse table-fixed w-full text-sm pt-3">
                <thead>
                    <tr>
                        <th class="border-b dark:border-slate-600 font-medium p-4 pb-3 text-slate-400 dark:text-slate-200 text-left">Data</th>
                        <th class="border-b dark:border-slate-600 font-medium p-4 pb-3 text-slate-400 dark:text-slate-200 text-left">Status</th>
                        <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Preço</th>
                    </tr>
                </thead>
                <tbody id="schedulingTableBody">
                </tbody>
            </table>
            <div class="grid grid-cols-1 place-items-center  py-3" id="msg-not-found">
                <div class="flex flex-col items-center justify-center">
                    <img class="warning-svg" src="/vendor/blade-heroicons/c-exclamation-circle.svg" alt="exclamation" style="width: 2em; height: 2em;">
                    <p class="text-center font-bold" style="width: 18rem;">
                        Não foram encontrados resultados para sua pesquisa.
                    </p>
                </div>
            </div>

        </div>

    </div>

    <!-- Cart -->
    <div class="bg-white dark:bg-gray-900 dark:text-white p-8" id="show_cart">
        <h1 class="text-2xl font-semibold mb-4">Carrinho de compras</h1>
        <div class="grid grid-cols-3 flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-4">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left font-semibold">Serviço</th>
                                <th class="text-left font-semibold">Preço</th>
                                <th class="text-left font-semibold">Duração</th>
                                <th class="text-left font-semibold">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="cart-items">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="md:w-1/4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Resumo</h2>
                    <div class="flex justify-between mb-2">
                        <span>Duração</span>
                        <span class="cart-total-duration"></span>
                    </div>

                    <!-- <hr class="my-2"> -->
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Total</span>
                        <span class="font-semibold cart-total-price"></span>
                    </div>

                    <x-filament::button icon="heroicon-s-calendar-days" id="btn_calendar" class="w-full opacity-50 rounded-md p-2 mt-3" @click="calendarScheduling()">
                        <span>Calendário</span>
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="row transition-opacity duration-500 delay-100 hidden" id="show_calendar">
        <div class="bkfd text-center p-3 mb-3 rounded">
            <h2 class="text-center font-bold text-xl mb-2">Dias Disponíveis</h2>
            <p class="text-sm">Para continuar o agendamento, clique nos dias em <strong class="text-green">verde</strong> no calendário abaixo.</p>
        </div>
        <div id='calendar'></div>
    </div>

    <!-- Available Time -->
    <div class="hidden" id="time_available">
    </div>

    <!-- Available Time -->
    <div class="hidden" id="scheduling_available_time">
        <div class="bkfd text-center p-3 mb-3 rounded">
            <h2 class="text-center font-bold text-xl mb-2">Informações Pessoais</h2>
            <p class="text-sm">Para finalizar o agendamento previsto para o dia <strong id="StrongDateAvailableTime"></strong>, por favor, preencha os campos abaixo.</p>
        </div>
        <form action="" method="post">
            <input type="hidden" id="schedulingData" name="schedulingData">

            <div>
                <div class="flex items-center justify-between gap-x-3 ">
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Nome Completo <sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
                        </span>
                    </label>
                </div>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" id="name" name="name" maxlength="255" minlength="2" required="required" wire:model="name" />
                </x-filament::input.wrapper>
            </div>

            <div>
                <div class="flex items-center justify-between gap-x-3 mt-2">
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            WhatsApp <sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
                        </span>
                    </label>
                </div>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" id="whatsapp" name="whatsapp" required="required" wire:model="whatsapp" x-mask="(99) 99999-9999" />
                </x-filament::input.wrapper>
            </div>

            <x-filament::button icon="heroicon-c-bookmark" class="w-full rounded-md p-2 mt-3" @click="scheduling()">
                <span>Agendar</span>
            </x-filament::button>
        </form>
    </div>
</x-filament::modal>
