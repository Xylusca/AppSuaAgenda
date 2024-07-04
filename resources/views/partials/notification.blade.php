<style>
    .notification {
        transition: top 0.3s ease;
    }

    .w-64 {
        width: 16rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .danger-svg {
        filter: invert(46%) sepia(64%) saturate(2708%) hue-rotate(330deg) brightness(98%) contrast(173%);
    }

    .success-svg {
        filter: invert(46%) sepia(100%) saturate(7500%) hue-rotate(90deg) brightness(100%) contrast(100%);
    }

    .info-svg {
        filter: invert(46%) sepia(100%) saturate(3000%) hue-rotate(200deg) brightness(103%) contrast(104%);
    }

    .warning-svg {
        filter: invert(46%) sepia(10%) saturate(8000%) hue-rotate(0deg) brightness(200%) contrast(100%);
    }
</style>

<div id="notifications-container"></div>

<script>
    let notificationCount = 0; // Variável para controlar o número de notificações

    function showNotification(text, type = 'success') {
        const icons = {
            success: '/vendor/blade-heroicons/o-check-circle.svg',
            danger: '/vendor/blade-heroicons/c-exclamation-circle.svg',
            info: '/vendor/blade-heroicons/c-information-circle.svg',
            warning: '/vendor/blade-heroicons/c-exclamation-circle.svg'
        };

        const notification = $(`
            <div class="z-50 absolute h-16 rounded-lg ${type} notification" style="position: fixed; right: 10px; top: ${10 + notificationCount * 70}px"> <!-- Ajuste da posição vertical -->
                <div class="flex items-center p-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
                    <div id="infoNotification-icon" class="mr-2 inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-700 dark:text-orange-200">
                        <img class="${type}-svg" src="${icons[type]}" alt="${type}" style="width: 2em; height: 2em;">
                    </div>
                    <div class="ms-3 text-sm font-normal w-64">${text}</div>
                </div>
            </div>
        `);

        $('#notifications-container').append(notification);
        notificationCount++; // Incrementando o contador de notificações

        setTimeout(() => {
            notification.addClass('hidden');
            setTimeout(() => {
                notification.remove();
                notificationCount--; // Decrementando o contador de notificações

                // Reajustar a posição vertical das notificações restantes
                $('#notifications-container').children().each(function(index) {
                    $(this).css('top', `${10 + index * 80}px`);
                });
            }, 500); // Remove o elemento após o desaparecimento
        }, 3000);
    }
</script>
