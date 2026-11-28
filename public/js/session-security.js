(() => {
    'use strict';

    if (window.sigacSessionSecurityInitialized) {
        return;
    }

    window.sigacSessionSecurityInitialized = true;

    /**
     * Ocultar el contenido cuando el navegador
     * pretende conservar la página en BFCache.
     */
    window.addEventListener('pagehide', (event) => {
        if (event.persisted) {
            document.documentElement.style.visibility = 'hidden';
        }
    });

    /**
     * Revalidar la sesión contra el servidor cuando
     * se restaura una página desde BFCache.
     */
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            window.location.reload();
        }
    });
})();
