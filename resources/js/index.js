(function () {
    function setCookieConsent(value) {
        var cfg = window.CookieConsent;
        var expires = new Date(Date.now() + cfg.duration * 1000).toUTCString();
        var cookie = cfg.cookieName + '=' + value + '; expires=' + expires + '; path=/; SameSite=' + cfg.sameSite;
        if (cfg.secure) {
            cookie += '; Secure';
        }
        document.cookie = cookie;
    }

    function updateGtag(granted) {
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'functional_storage': 'granted',
                'security_storage': 'granted',
                'analytics_storage': granted ? 'granted' : 'denied',
                'ad_storage': granted ? 'granted' : 'denied',
                'ad_user_data': granted ? 'granted' : 'denied',
                'ad_personalization': granted ? 'granted' : 'denied',
            });
        } else if (granted) {
            // gtag not yet loaded on first accept — reload to activate GTM with new consent
            window.location.reload();
        }
    }

    function updatePosthog(granted) {
        if (!window.__posthogConfig) {
            return;
        }
        if (granted) {
            if (window.posthog && typeof window.posthog.has_opted_out_capturing === 'function') {
                window.posthog.opt_in_capturing();
            } else {
                // PostHog not yet loaded — reload so the server-side snippet initializes it
                window.location.reload();
            }
        } else {
            if (window.posthog && typeof window.posthog.opt_out_capturing === 'function') {
                window.posthog.opt_out_capturing();
            }
        }
    }

    function hideBanner() {
        var el = document.getElementById('cookie-consent-banner');
        if (el) {
            el.style.display = 'none';
        }
    }

    window.__cookieConsent = {
        accept: function () {
            setCookieConsent('full');
            updateGtag(true);
            updatePosthog(true);
            hideBanner();
        },
        refuse: function () {
            setCookieConsent('none');
            updateGtag(false);
            updatePosthog(false);
            hideBanner();
        },
    };
})();
