@extends('layouts.app')

@section('content')

<style>
    .btn-fixed {
        position: fixed;
        bottom: 55px;
        right: 20px;
        z-index: 1;
    }
</style>

<!-- Product - Carousel -->
@include('partials.home.ProductCarousel')

<!-- Modal -->
<x-filament::modal width="4xl" dark id="myModal">
    <!-- button Cart  -->
    <x-slot name="trigger">
        <x-filament::button icon="heroicon-s-shopping-cart" class="btn-fixed" @click="showCart()">
            <span id="cart-count" class="hidden"></span>
        </x-filament::button>
    </x-slot>

    <x-slot name="heading">
        <x-filament::button icon="heroicon-m-arrow-left-circle" id="btn_back" class="flex" @click="goToBack()">
            <span>Voltar</span>
        </x-filament::button>
    </x-slot>

    <!-- Cart -->
    <div class="bg-white dark:bg-gray-900 dark:text-white p-8" id="cart_cont">
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
    <div class="row transition-opacity duration-500 delay-100" id="calendar_cont">
        <div id='calendar'></div>
    </div>

    <!-- Available Time -->
    <div id="time_available" class="hidden">
    </div>

    <!-- Available Time -->
    <div id="scheduling_available_time" class="hidden">
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

<script src="{{asset('js/cart-modal.js')}}"></script>
@endsection