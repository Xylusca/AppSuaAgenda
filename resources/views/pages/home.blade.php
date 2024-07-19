@extends('layouts.app')

@section('content')

<style>
    :root {
        --cor-principal: rgba(76, 91, 106, 0.5);
        /* Exemplo de um verde */
    }

    .btn-fixed {
        position: fixed;
        bottom: 55px;
        right: 20px;
        z-index: 1;
    }

    .bkfd {
        background-color: var(--cor-principal);
    }

    .text-green {
        color: green;
    }

    .fc-view-harness {
        background-color: var(--cor-principal);
    }

    .ml-btn {
        margin-left: 0.5rem;
    }

    .mb-3 {
        margin-bottom: 0.5rem;
    }
</style>

<!-- Product - Carousel -->
@include('partials.home.ProductCarousel')

<!-- Product - Carousel -->
@include('partials.home.FullSchedulingModal')

<script src="{{ asset('js/full-scheduling-modal.js')}}"></script>
@endsection
