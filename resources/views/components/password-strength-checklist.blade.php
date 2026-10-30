<div id="{{ $inputId }}-requirements" class="mt-2 text-xs text-slate-500" aria-live="polite">
    <p class="font-semibold mb-1">Your password needs:</p>
    <ul class="grid grid-cols-2 gap-x-3 gap-y-1">
        <li data-password-criterion="length"><span aria-hidden="true">○</span> At least 8 characters</li>
        <li data-password-criterion="uppercase"><span aria-hidden="true">○</span> Uppercase letter</li>
        <li data-password-criterion="lowercase"><span aria-hidden="true">○</span> Lowercase letter</li>
        <li data-password-criterion="number"><span aria-hidden="true">○</span> Number</li>
        <li data-password-criterion="symbol"><span aria-hidden="true">○</span> Special character</li>
    </ul>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-password-strength]').forEach((input) => {
                const requirements = document.getElementById(`${input.id}-requirements`);
                if (!requirements) {
                    return;
                }

                const checks = {
                    uppercase: /\p{Lu}/u,
                    lowercase: /\p{Ll}/u,
                    number: /\p{N}/u,
                    symbol: /[\p{Z}\p{S}\p{P}]/u,
                };

                const updateRequirements = () => {
                    const value = input.value;
                    requirements.querySelectorAll('[data-password-criterion]').forEach((item) => {
                        const criterion = item.dataset.passwordCriterion;
                        const met = criterion === 'length'
                            ? [...value].length >= 8
                            : checks[criterion].test(value);

                        item.classList.toggle('text-emerald-700', Boolean(met));
                        item.classList.toggle('text-slate-500', !met);
                        item.querySelector('span').textContent = met ? '✓' : '○';
                    });
                };

                input.addEventListener('input', updateRequirements);
                updateRequirements();
            });
        });
    </script>
@endonce
