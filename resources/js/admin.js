document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.$ === 'undefined') return;

    // 1. Универсальное модальное окно подтверждения удаления
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        window.$(modal).on('show.bs.modal', function (event) {
            const button = window.$(event.relatedTarget);
            const url = button.data('url');
            const text = button.data('text') || 'Вы действительно хотите удалить эту запись?';

            window.$(this).find('#deleteConfirmForm').attr('action', url);
            window.$(this).find('#deleteConfirmText').text(text);
        });
    }

    // 2. Автоматическое плавное скрытие flash-уведомлений через 4 секунды
    const alerts = document.querySelectorAll('.content .alert-dismissible');
    if (alerts.length > 0) {
        setTimeout(() => {
            window.$(alerts).fadeTo(500, 0).slideUp(300, function () {
                window.$(this).alert('close');
            });
        }, 4000);
    }
});
