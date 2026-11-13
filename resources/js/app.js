//

const onboardingForm = document.querySelector('[data-onboarding-form]');

if (onboardingForm instanceof HTMLFormElement) {
    const loginPaths = {
        siswa: '/login/siswa',
        guru: '/login/guru',
    };

    const submitButton = onboardingForm.querySelector('button[type="submit"]');

    onboardingForm.querySelectorAll('input[name="role"]').forEach((roleInput) => {
        roleInput.addEventListener('change', () => {
            const loginPath = loginPaths[roleInput.value];

            if (!loginPath || !(submitButton instanceof HTMLButtonElement)) {
                return;
            }

            onboardingForm.action = loginPath;
            submitButton.disabled = false;
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
