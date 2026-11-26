document.addEventListener('DOMContentLoaded', () => {
    // 1. Универсальное модальное окно подтверждения удаления
    const modal = document.getElementById('deleteConfirmModal');
    if (modal && typeof window.$ !== 'undefined') {
        window.$(modal).on('show.bs.modal', function (event) {
            const button = window.$(event.relatedTarget);
            const url = button.data('url');
            const text = button.data('text') || 'Вы действительно хотите удалить эту запись?';

            window.$(this).find('#deleteConfirmForm').attr('action', url);
            window.$(this).find('#deleteConfirmText').text(text);
        });
    }

    // 2. Плавающее Toast-уведомление с таймером на 3 секунды
    const toast = document.getElementById('adminToast');
    if (toast) {
        requestAnimationFrame(() => toast.classList.add('is-visible'));

        let remaining = 3;
        const numEl = toast.querySelector('.admin-toast__timer-num');

        const closeToast = () => {
            clearInterval(interval);
            toast.classList.remove('is-visible');
            setTimeout(() => toast.remove(), 250);
        };

        toast.addEventListener('click', closeToast);

        const interval = setInterval(() => {
            remaining -= 1;
            if (numEl && remaining > 0) {
                numEl.textContent = remaining;
            }
            if (remaining <= 0) {
                closeToast();
            }
        }, 1000);
    }
});
