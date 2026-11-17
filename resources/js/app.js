//

const onboardingForm = document.querySelector('[data-onboarding-form]');

if (onboardingForm instanceof HTMLFormElement) {
    const roleWarning = onboardingForm.querySelector('[data-role-warning]');

    onboardingForm.querySelectorAll('input[name="role"]').forEach((roleInput) => {
        roleInput.addEventListener('invalid', () => {
            if (roleWarning instanceof HTMLElement) {
                roleWarning.hidden = false;
            }
        });

        roleInput.addEventListener('change', () => {
            if (roleWarning instanceof HTMLElement) {
                roleWarning.hidden = true;
            }
        });
    });
}

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    if (!(toggle instanceof HTMLButtonElement)) {
        return;
    }

    toggle.addEventListener('click', () => {
        const passwordInput = toggle.closest('.login-password-wrap')?.querySelector('input');

        if (!(passwordInput instanceof HTMLInputElement)) {
            return;
        }

        const isPasswordVisible = passwordInput.type === 'text';

        passwordInput.type = isPasswordVisible ? 'password' : 'text';
        toggle.setAttribute('aria-pressed', String(!isPasswordVisible));
        toggle.setAttribute(
            'aria-label',
            isPasswordVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi',
        );
    });
});
