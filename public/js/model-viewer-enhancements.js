(() => {
    const injectStyles = () => {
        const style = document.createElement('style');
        style.textContent = `
            #stage.viewer-touch canvas { touch-action: none; }
            #stage.viewer-portrait { min-height: calc(var(--viewer-vh, 100vh) - 60px); }
            @media (min-width: 1024px) and (max-width: 1279px) {
                #stage { min-height: var(--viewer-vh, 100vh) !important; }
                .preview-sidebar { max-height: var(--viewer-vh, 100vh) !important; }
                #viewer-tools { max-width: calc(100% - 2rem); }
            }
            @media (max-width: 767px) {
                .preview-sidebar {
                    position: fixed !important;
                    left: 0; right: 0; bottom: 0;
                    z-index: 45;
                    max-height: min(78svh, 680px) !important;
                    border-left: 0 !important;
                    border-top: 1px solid #cbd5e1 !important;
                    border-radius: 1.25rem 1.25rem 0 0;
                    box-shadow: 0 -18px 45px -24px rgba(15, 23, 42, .45) !important;
                    transform: translateY(calc(100% - 3.75rem));
                    transition: transform .22s ease;
                }
                .preview-sidebar.mobile-open { transform: translateY(0); }
                .mobile-controls-toggle {
                    position: fixed; right: 1rem; bottom: 1rem; z-index: 50;
                    border: 1px solid rgba(34, 211, 238, .45);
                    border-radius: 999px; background: #0e7490; color: white;
                    padding: .7rem 1rem; font-size: .75rem; font-weight: 700;
                    box-shadow: 0 10px 24px -8px rgba(8, 47, 73, .65);
                }
                #stage { min-height: calc(var(--viewer-vh, 100svh) - 60px) !important; }
                #viewer-tools { bottom: 4.75rem !important; }
                .room-preview-controls { left: 1rem !important; right: 1rem !important; bottom: 5rem !important; }
                .preview-header { padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); }
                #viewer-toolbar { bottom: calc(.75rem + env(safe-area-inset-bottom)); }
                .mobile-controls-toggle { bottom: calc(.75rem + env(safe-area-inset-bottom)); }
            }
            @media (min-width: 768px) and (max-width: 1023px) {
                .preview-sidebar {
                    position: fixed !important;
                    top: 0; right: 0; bottom: 0;
                    width: min(390px, 46vw);
                    max-height: none !important;
                    border-left: 1px solid #cbd5e1 !important;
                    border-top: 0 !important;
                    box-shadow: -18px 0 45px -24px rgba(15, 23, 42, .45) !important;
                    transform: translateX(calc(100% - 3.75rem));
                    transition: transform .22s ease;
                }
                .preview-sidebar.mobile-open { transform: translateX(0); }
                #stage { min-height: var(--viewer-vh, 100vh) !important; }
                .mobile-controls-toggle { top: 1rem; right: 1rem; bottom: auto; }
                #viewer-tools { right: 1rem !important; max-width: calc(100% - 5rem); }
            }
            @media (max-width: 420px) {
                #viewer-toolbar { left: .75rem; right: .75rem; max-width: none; }
                #viewer-toolbar button { padding-left: .55rem; padding-right: .55rem; }
                #viewer-tools { left: .75rem !important; right: .75rem !important; }
                .stage-title-copy { max-width: 70vw; }
            }
            .room-preview-controls { position: absolute; left: 1rem; bottom: 4.5rem; z-index: 25; }
            .room-preview-controls input[type=range] { width: 5rem; accent-color: #22d3ee; }
            #stage.room-preview-active { background-image: none !important; }
            #stage.room-preview-active::before {
                content: ''; position: absolute; inset: 0; z-index: 0;
                background-image: var(--room-image); background-size: cover; background-position: center;
                opacity: var(--room-opacity, .9);
            }
            #stage.room-preview-active > canvas { position: relative; z-index: 1; }
        `;
        document.head.append(style);
    };

    const addMobileControls = () => {
        const sidebar = document.querySelector('.preview-sidebar');
        const stage = document.querySelector('#stage');
        if (!sidebar || !stage || document.querySelector('.mobile-controls-toggle')) return;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'mobile-controls-toggle';
        button.textContent = 'Controls';
        button.setAttribute('aria-expanded', 'false');
        button.onclick = () => {
            const open = sidebar.classList.toggle('mobile-open');
            button.textContent = open ? 'Close controls' : 'Controls';
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        stage.append(button);
    };

    const setupResponsiveViewport = () => {
        const stage = document.querySelector('#stage');
        if (!stage) return;
        const update = () => {
            const height = window.visualViewport?.height || window.innerHeight;
            document.documentElement.style.setProperty('--viewer-vh', `${height}px`);
            stage.classList.toggle('viewer-portrait', height >= window.innerWidth);
            stage.classList.toggle('viewer-touch', matchMedia('(pointer: coarse)').matches);
        };
        update();
        window.addEventListener('resize', update, { passive: true });
        window.addEventListener('orientationchange', () => setTimeout(update, 120), { passive: true });
        window.visualViewport?.addEventListener('resize', update, { passive: true });
    };

    const addRoomPreview = () => {
        const stage = document.querySelector('#stage');
        const tools = document.querySelector('#viewer-tools');
        if (!stage || !tools || document.querySelector('#room-preview')) return;

        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.className = 'hidden';
        input.id = 'room-preview-input';
        stage.append(input);

        const button = document.createElement('button');
        button.type = 'button';
        button.id = 'room-preview';
        button.className = 'compact-control rounded-lg px-2.5 py-2 text-[11px] font-medium text-slate-200 hover:bg-white/10';
        button.title = 'Preview the model in a room';
        button.textContent = 'Room preview';
        tools.append(button);

        const controls = document.createElement('div');
        controls.className = 'room-preview-controls hidden rounded-xl border border-white/10 bg-slate-950/85 px-3 py-2 text-xs text-slate-200 shadow-xl backdrop-blur';
        controls.innerHTML = '<div class="flex items-center gap-2"><span>Room image</span><input id="room-opacity" type="range" min="0.35" max="1" step="0.05" value="0.9" aria-label="Room image opacity"><button id="room-clear" type="button" class="rounded-md border border-white/20 px-2 py-1 text-[10px]">Clear</button></div>';
        stage.append(controls);

        const setRoom = (url) => {
            stage.classList.toggle('room-preview-active', Boolean(url));
            stage.style.setProperty('--room-image', url ? `url("${url}")` : 'none');
            controls.classList.toggle('hidden', !url);
            button.textContent = url ? 'Change room' : 'Room preview';
            window.dispatchEvent(new CustomEvent('model-viewer:room-image', { detail: { url } }));
        };

        button.onclick = () => input.click();
        input.onchange = () => {
            const file = input.files?.[0];
            if (file) setRoom(URL.createObjectURL(file));
        };
        controls.querySelector('#room-clear').onclick = () => {
            input.value = '';
            setRoom(null);
        };
        controls.querySelector('#room-opacity').oninput = (event) => {
            stage.style.setProperty('--room-opacity', event.target.value);
            window.dispatchEvent(new CustomEvent('model-viewer:room-opacity', { detail: { opacity: event.target.value } }));
        };
    };

    const init = () => {
        injectStyles();
        setupResponsiveViewport();
        addMobileControls();
        const observer = new MutationObserver(() => {
            addRoomPreview();
            if (document.querySelector('#viewer-tools')) observer.disconnect();
        });
        observer.observe(document.body, { childList: true, subtree: true });
        addRoomPreview();
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
})();
