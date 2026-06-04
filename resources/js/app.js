const root = document.documentElement;
const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
const sidebarClosers = document.querySelectorAll('[data-sidebar-close]');
const desktopQuery = window.matchMedia('(min-width: 1024px)');

const savedState = localStorage.getItem('sidebar-collapsed');

function syncSidebarButton() {
    const isOpen = root.classList.contains('sidebar-open') || (! root.classList.contains('sidebar-collapsed') && desktopQuery.matches);

    sidebarToggles.forEach((button) => {
        button.setAttribute('aria-expanded', String(isOpen));
    });
}

function openSidebar() {
    root.classList.add('sidebar-open');
    root.classList.remove('sidebar-collapsed');
    if (desktopQuery.matches) {
        localStorage.setItem('sidebar-collapsed', 'false');
    }
    syncSidebarButton();
}

function closeSidebar() {
    root.classList.remove('sidebar-open');
    if (desktopQuery.matches) {
        root.classList.add('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', 'true');
    }
    syncSidebarButton();
}

if (desktopQuery.matches && savedState === 'true') {
    root.classList.add('sidebar-collapsed');
}

sidebarToggles.forEach((button) => {
    button.addEventListener('click', () => {
        if (desktopQuery.matches) {
            if (root.classList.contains('sidebar-collapsed')) {
                openSidebar();
            } else {
                closeSidebar();
            }

            return;
        }

        root.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    });
});

sidebarClosers.forEach((button) => {
    button.addEventListener('click', closeSidebar);
});

desktopQuery.addEventListener('change', () => {
    root.classList.remove('sidebar-open');
    if (desktopQuery.matches && localStorage.getItem('sidebar-collapsed') === 'true') {
        root.classList.add('sidebar-collapsed');
    } else {
        root.classList.remove('sidebar-collapsed');
    }
    syncSidebarButton();
});

syncSidebarButton();
