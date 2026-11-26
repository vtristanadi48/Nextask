//

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

const dashboardShell = document.querySelector('[data-dashboard-shell]');

if (dashboardShell instanceof HTMLElement) {
    const sidebarToggle = dashboardShell.querySelector('[data-sidebar-toggle]');
    const sidebarClose = dashboardShell.querySelector('[data-sidebar-close]');
    const sidebar = dashboardShell.querySelector('#dashboard-sidebar');
    const mobileSidebar = window.matchMedia('(max-width: 900px)');

    if (
        sidebarToggle instanceof HTMLButtonElement
        && sidebarClose instanceof HTMLButtonElement
        && sidebar instanceof HTMLElement
    ) {
        const setSidebarOpen = (isOpen) => {
            dashboardShell.dataset.sidebarOpen = String(isOpen);
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
            sidebarToggle.setAttribute('aria-label', isOpen ? 'Tutup navigasi' : 'Buka navigasi');
            sidebar.inert = mobileSidebar.matches && !isOpen;
        };

        setSidebarOpen(false);

        sidebarToggle.addEventListener('click', () => {
            setSidebarOpen(dashboardShell.dataset.sidebarOpen !== 'true');
        });

        sidebarClose.addEventListener('click', () => {
            setSidebarOpen(false);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        });

        mobileSidebar.addEventListener('change', () => setSidebarOpen(false));
    }
}

const featureToast = document.querySelector('[data-feature-toast]');
let featureToastTimeout;

document.addEventListener('click', (event) => {
    const clickedElement = event.target instanceof Element ? event.target : null;
    const noticeLink = clickedElement?.closest('[data-feature-notice]');

    if (!(noticeLink instanceof HTMLAnchorElement)) {
        return;
    }

    event.preventDefault();

    if (!(featureToast instanceof HTMLElement)) {
        return;
    }

    featureToast.textContent = noticeLink.dataset.featureNotice || 'Segera hadir';
    featureToast.hidden = false;
    window.clearTimeout(featureToastTimeout);
    featureToastTimeout = window.setTimeout(() => {
        featureToast.hidden = true;
    }, 2800);
});

const dashboardDropdowns = Array.from(document.querySelectorAll('[data-dashboard-dropdown]'))
    .map((dropdown) => ({
        dropdown,
        toggle: dropdown.querySelector('[data-navbar-dropdown-toggle]'),
        panel: dropdown.querySelector('[data-navbar-dropdown-panel]'),
    }))
    .filter(({ toggle, panel }) => (
        toggle instanceof HTMLButtonElement && panel instanceof HTMLElement
    ));

if (dashboardDropdowns.length > 0) {
    const setDropdownOpen = ({ toggle, panel }, isOpen) => {
        toggle.setAttribute('aria-expanded', String(isOpen));
        panel.hidden = !isOpen;
    };

    const closeDashboardDropdowns = (except = null) => {
        dashboardDropdowns.forEach((dropdown) => {
            if (dropdown !== except) {
                setDropdownOpen(dropdown, false);
            }
        });
    };

    dashboardDropdowns.forEach((dropdown) => {
        dropdown.toggle.addEventListener('click', () => {
            const shouldOpen = dropdown.toggle.getAttribute('aria-expanded') !== 'true';

            closeDashboardDropdowns();
            setDropdownOpen(dropdown, shouldOpen);
        });
    });

    document.addEventListener('click', (event) => {
        const clickedElement = event.target instanceof Element ? event.target : null;

        if (!clickedElement?.closest('[data-dashboard-dropdown]')) {
            closeDashboardDropdowns();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const openDropdown = dashboardDropdowns.find(
            ({ toggle }) => toggle.getAttribute('aria-expanded') === 'true',
        );

        closeDashboardDropdowns();
        openDropdown?.toggle.focus();
    });
}
