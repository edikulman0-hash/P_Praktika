@if (session('success') || session('error'))
    @php
        $isSuccess = session()->has('success');
        $message = $isSuccess ? session('success') : session('error');
        $bgClass = $isSuccess ? 'bg-success' : 'bg-danger';
        $iconClass = $isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle';
    @endphp

    <div id="adminToast" class="admin-toast {{ $bgClass }} shadow-lg" role="alert">
        <div class="admin-toast__body">
            <i class="fas {{ $iconClass }} mr-2"></i>
            <span>{{ $message }}</span>
        </div>
        <button type="button" class="admin-toast__close" aria-label="Закрыть" title="Закрыть">
            <svg class="admin-toast__ring" viewBox="0 0 28 28">
                <circle class="admin-toast__ring-bg" cx="14" cy="14" r="11"></circle>
                <circle class="admin-toast__ring-progress" cx="14" cy="14" r="11"></circle>
            </svg>
            <span class="admin-toast__timer-num">3</span>
            <i class="fas fa-times admin-toast__cross"></i>
        </button>
    </div>
@endif
