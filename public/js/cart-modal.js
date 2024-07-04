var cart = [];

function goToBack() {
    if (!$('#calendar_cont').hasClass('hidden')) {
        showCart();
    } else if (!$('#time_available').hasClass('hidden')) {
        $('#btn_back').removeClass('hidden');
        showCalender();
    } else if (!$('#scheduling_available_time').hasClass('hidden')) {
        $('#btn_back').removeClass('hidden');
        showTimeAvailable();
    }
}

function showCart() {
    $('#btn_back').addClass('hidden');
    $('#cart_cont').removeClass('hidden');
    $('#calendar_cont').addClass('hidden');
    $('#time_available').addClass('hidden');
    $('#scheduling_available_time').addClass('hidden');
}

function showCalender() {
    $('#btn_back').removeClass('hidden');
    $('#calendar_cont').removeClass('hidden');
    $('#cart_cont').addClass('hidden');
    $('#time_available').addClass('hidden');
    $('#scheduling_available_time').addClass('hidden');
}

function showTimeAvailable() {
    $('#btn_back').removeClass('hidden');
    $('#time_available').removeClass('hidden');
    $('#cart_cont').addClass('hidden');
    $('#calendar_cont').addClass('hidden');
    $('#scheduling_available_time').addClass('hidden');
}

function showSchedulingAvailableTime() {
    $('#btn_back').removeClass('hidden');
    $('#scheduling_available_time').removeClass('hidden');
    $('#time_available').addClass('hidden');
    $('#cart_cont').addClass('hidden');
    $('#calendar_cont').addClass('hidden');
}

// Cart
$(document).ready(function () {
    var cartCount = document.getElementById('cart-count');

    $(".add-to-cart").click(function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = parseFloat($(this).data('price'));
        var duration = $(this).data('duration');
        var img = $(this).data('img');

        var product = {
            id: id,
            name: name,
            price: price,
            duration: duration,
            img: img,
        };

        var index = cart.findIndex(item => item.id === product.id);
        if (index !== -1) {
            showNotification('Produto já está no carrinho!', 'warning');
        } else {
            cart.push(product);
            showNotification('Produto foi adicionado ao carrinho!');
            displayCart();
        }

        count_cart()
    });

    function count_cart() {
        if (cart.length > 0) {
            cartCount.textContent = cart.length;
            cartCount.classList.remove('hidden'); // Mostra o contador se houver itens no carrinho
            $('#btn_calendar').click(function (event) {
                event.preventDefault();
            });
            $('#btn_calendar').removeClass('opacity-50');
        } else {
            $('#btn_calendar').addClass('opacity-50');
            cartCount.classList.add('hidden'); // Esconde o contador se o carrinho estiver vazio
        }
    }

    function displayCart() {
        $(".cart-items").empty();
        var totalDuration = 0;
        var totalPrice = 0;

        cart.forEach(function (item) {
            $(".cart-items").append(`
                    <tr>
                        <td class="py-4">
                            <div class="flex items-center flex-col md:flex-row">
                                <img class="w-16 hidden sm:block" style="margin-right: 8px;" src="storage/${item.img}" alt="Product image">
                                <span class="font-semibold md:text-left" >${item.name}</span>
                            </div>
                        </td>
                        <td class="py-4 sm:text-sm">R$ ${item.price.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="py-4">${item.duration} min</td>
                        <td class="py-4">
                            <button class="remove-item" data-id="${item.id}">
                                <img class="danger-svg" src="/vendor/blade-heroicons/c-trash.svg" style="margin-left: .5rem; width: 1.5rem;" alt="excluir">
                            </button>
                        </td>
                    </tr>
                `);

            totalDuration += item.duration;
            totalPrice += item.price;
        });

        $(".cart-total-duration").text(`${totalDuration} min`);
        $(".cart-total-price").text(`R$ ${totalPrice.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
    }

    $(document).on('click', '.remove-item', function () {
        var itemId = $(this).data('id');
        var index = cart.findIndex(item => item.id === itemId);
        if (index !== -1) {
            cart.splice(index, 1);
            displayCart();
            showNotification('Produto foi removido do carrinho!');
            count_cart();
        }
    });


});

// Calendar
function calendarScheduling() {
    // $('#load').removeClass('hidden')
    if (cart.length > 0) {
        showCalender();

        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next'
            },
            contentHeight: 400,
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '/api/available-days',
                    method: 'POST',
                    data: {
                        id_services: cart.map(function (item) {
                            return item.id;
                        }).join(','),
                    },
                    dataType: 'json',
                    success: function (response) {
                        successCallback(response);
                        calendar.render()
                        // $('#load').addClass('hidden')
                    },
                    error: function (xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            for (var key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    var errorMessages = errors[key];
                                    errorMessages.forEach(function (error) {
                                        showNotification(error, 'danger');
                                    });
                                }
                            }
                        } else {
                            showNotification("Ocorreu um erro desconhecido.", 'danger');
                        }

                        failureCallback(error);
                    }
                });
            },
            eventClick: function (info) {
                showTimeAvailable();
                time_available(info.event._def.extendedProps.allTimeAvailable, info.event.start);
            }
        });

    } else {
        $('#load').addClass('hidden')
        showNotification('Por favor, adicione um produto ao carrinho!', 'warning');
    }

}

// Available Time
function time_available(time, date) {
    $(document).ready(function () {
        var $div = $('#time_available');

        $div.empty();

        var formattedDate = formatarDataExtenso(date);

        $div.append(
            '<div class="bkfd text-center p-2 rounded">' +
            '   <h2 class="text-center font-bold text-xl mb-2">Horários Disponíveis</h2>' +
            '   <p class="text-sm">Seu agendamento é para o dia <strong>' + formattedDate + '</strong>. Clique nos horários em <strong class="text-green">verde</strong> abaixo para continuar.</p>' +
            '</div>'
        );

        var $ul = $('<ul></ul>').css({
            'display': 'flex',
            'flex-wrap': 'wrap',
            'list-style-type': 'none',
            'justify-content': 'center',
            'padding': '0',
            'margin': '0'
        });

        time.forEach(function (item) {
            var $li = $('<li></li>').text(item.replace(/:\d{2}(?=\s|$)/, '')).addClass('rounded-full text-center select-none').css({
                'background': '#7d9161',
                'width': '6rem',
                'margin': '0.5rem',
                'padding': '0.2rem 0',
                'color': 'white',
            });

            $ul.append($li);

            $li.on('click', function () {
                var formattedDatePt = formatarDataExtenso(date);
                $('#StrongDateAvailableTime').empty();
                $('#StrongDateAvailableTime').append(formattedDatePt + " ás " + item);

                var formattedDate = new Date(date).toLocaleDateString('pt-BR');
                $('#schedulingData').val(formattedDate + " " + item);

                if (localStorage.getItem('name') && localStorage.getItem('whatsapp')) {
                    $('#name').val(localStorage.getItem('name'));
                    $('#whatsapp').val(localStorage.getItem('whatsapp'));
                }

                showSchedulingAvailableTime()
            });

        });

        $div.append($ul);
    });
}

//  Scheduling Available Time
function scheduling(dateTime) {
    $('#load').removeClass('hidden')

    var name = $('#name').val();
    var whatsapp = $('#whatsapp').val();

    // Guardar informações no localStorage
    if (name && whatsapp) {
        localStorage.setItem('name', name);
        localStorage.setItem('whatsapp', whatsapp);

        $.ajax({
            url: '/api/schedule',
            method: 'POST',
            data: {
                name: name,
                whatsapp: whatsapp,
                schedulingData: $('#schedulingData').val(),
                id_services: cart.map(function (item) {
                    return item.id;
                }).join(','),
            },
            dataType: 'json',
            success: function (response) {
                // Lógica a ser executada após o sucesso
                if (response['type'] === "success") {
                    showNotification(response['message'], response['type']);
                } else {
                    showNotification(response['message'], response['type']);
                }

                setTimeout(function () {
                    $('.fi-modal-close-btn').click();
                }, 3);

                $('#load').addClass('hidden')
            },
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    for (var key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            var errorMessages = errors[key];
                            errorMessages.forEach(function (error) {
                                showNotification(error, 'danger');
                            });
                        }
                    }
                } else {
                    showNotification("Ocorreu um erro desconhecido.", 'danger');
                }

                $('#load').addClass('hidden')
                failureCallback(error);
            }
        });
    } else {
        showNotification("Por favor, preencha os campos 'Nome Completo' e 'WhatsApp'", 'warning');
        $('#load').addClass('hidden')
    }
}

// Função para formatar a data no formato desejado
function formatarDataExtenso(data) {
    // Cria uma nova data a partir do objeto Date recebido
    var dataObjeto = new Date(data);

    // Opções para formatar a data
    var opcoes = { day: '2-digit', month: 'long', year: 'numeric' };

    // Retorna a data formatada no formato "02 de julho de 2024"
    return dataObjeto.toLocaleDateString('pt-BR', opcoes);
}
