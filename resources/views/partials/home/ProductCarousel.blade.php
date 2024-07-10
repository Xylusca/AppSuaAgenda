<style>
    @media (min-height: 700px) and (max-width: 450px) {
        .dsc-h {
            height: 15rem !important;
        }

        .fs-h5-mobile {
            font-size: 2rem;
        }

        .fs-p-mobile {
            font-size: 1.3rem;
        }

        .owl-carousel .owl-item img {
            height: 22rem;
        }
    }
</style>

<div class="owl-carousel owl-theme">
    @foreach($services as $service)
    <div class="mx-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 md:w-auto">
        <img class="p-8 rounded-t-lg w-full" src="{{ asset('storage/' . $service->image_path) }}" alt="product image" />
        <div class="px-3 py-2">
            <h5 class="text-xl fs-h5-mobile font-semibold tracking-tight text-gray-900 dark:text-white py-2">{{ $service->name }}</h5>
            <p class="mt-1 fs-p-mobile h-32 overflow-hidden dsc-h">{{ $service->desc }}</p>
            <div class="flex m-8">
                <div style="flex-basis: 90%">
                    <div class="flex items-center">
                        <x-heroicon-o-clock class="w-4 h-4 mr-2" />
                        <p>{{ $service->duration }} min</p>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                </div>
                <button data-id="<?php echo $service->id ?>" data-name="<?php echo $service->name ?>" data-price="<?php echo $service->price ?>" data-duration="<?php echo $service->duration ?>" data-img="<?php echo $service->image_path ?>" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);" class="add-to-cart fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50" type="button">
                    Adicionar carrinho
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    $(document).ready(function() {
        $(".owl-carousel").owlCarousel({
            loop: true,
            // responsiveClass:true,
            responsive: {
                0: {
                    items: 1,
                    nav: false,
                },
                500: {
                    items: 2,
                    loop: false,
                    nav: true,
                },
                800: {
                    items: 3,
                    loop: false,
                    nav: true,
                }
            }
        });
    });
</script>
