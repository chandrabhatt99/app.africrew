<button
    type="button"
    data-password-visibility="{{ $inputId }}"
    data-password-label="{{ $label ?? 'password' }}"
    aria-label="Show {{ $label ?? 'password' }}"
    aria-pressed="false"
    title="Show {{ $label ?? 'password' }}"
    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 rounded-r-xl"
>
    <svg data-password-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/>
        <circle cx="12" cy="12" r="3"/>
    </svg>
    <svg data-password-eye-off viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5 hidden" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 8 2.25 12 2.25 12s3.5 7 9.75 7c1.25 0 2.4-.3 3.4-.8"/>
    </svg>
</button>

@once
    <script>
        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-password-visibility]');
            if (!button) return;

            const input = document.getElementById(button.dataset.passwordVisibility);
            if (!input) return;

            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));

            const action = visible ? 'Hide' : 'Show';
            const label = `${action} ${button.dataset.passwordLabel || 'password'}`;
            button.setAttribute('aria-label', label);
            button.title = label;
            button.querySelector('[data-password-eye]').classList.toggle('hidden', visible);
            button.querySelector('[data-password-eye-off]').classList.toggle('hidden', !visible);
        });
    </script>
@endonce
