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
