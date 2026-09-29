import { driver } from 'driver.js';

/**
 * Admin panel product tour, powered by driver.js.
 *
 * Bootstrapped by `window.__adminTour` (see the `admin-tour-bootstrap`
 * render hook in AdminPanelProvider), which carries the CSRF token, the
 * "mark complete" endpoint, and whether this user has already finished it.
 */
function buildSteps(t) {
    return [
        {
            popover: {
                title: t.welcomeTitle,
                description: t.welcomeDescription,
            },
        },
        {
            element: '.fi-sidebar-nav',
            popover: {
                title: t.navTitle,
                description: t.navDescription,
                side: 'right',
                align: 'start',
            },
        },
        {
            element: '.fi-global-search',
            popover: {
                title: t.searchTitle,
                description: t.searchDescription,
                side: 'bottom',
                align: 'start',
            },
        },
        {
            element: '.fi-topbar-database-notifications-btn',
            popover: {
                title: t.notificationsTitle,
                description: t.notificationsDescription,
                side: 'bottom',
                align: 'end',
            },
        },
        {
            element: '.fi-user-menu',
            popover: {
                title: t.profileTitle,
                description: t.profileDescription,
                side: 'bottom',
                align: 'end',
            },
        },
        {
            popover: {
                title: t.doneTitle,
                description: t.doneDescription,
            },
        },
    ].filter((step) => !step.element || document.querySelector(step.element));
}

function markComplete(config) {
    if (!config?.completeUrl) return;

    fetch(config.completeUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
        keepalive: true,
    }).catch(() => {});
}

function startAdminTour() {
    const config = window.__adminTour;
    if (!config) return;

    const steps = buildSteps(config.strings);
    if (steps.length === 0) return;

    const tour = driver({
        showProgress: true,
        allowClose: true,
        overlayOpacity: 0.55,
        popoverClass: 'admin-tour-popover',
        nextBtnText: config.strings.next,
        prevBtnText: config.strings.previous,
        doneBtnText: config.strings.done,
        progressText: config.strings.progress,
        steps,
        onDestroyed: () => markComplete(config),
    });

    tour.drive();
}

window.startAdminTour = startAdminTour;

document.addEventListener('DOMContentLoaded', () => {
    const config = window.__adminTour;
    if (config?.autoStart) {
        // Let Filament finish painting widgets/sidebar before targeting them.
        window.setTimeout(startAdminTour, 400);
    }
});
