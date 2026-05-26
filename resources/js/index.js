(function () {
    // Minimal TCF 2.0 stub — signals to AdSense that a CMP is active (prevents Funding Choices from loading).
    // gdprApplies: false avoids TC string validation while still satisfying the __tcfapi presence check.
    (function () {
        let _callbacks = [];

        window.__tcfapi = function (cmd, version, callback) {
            switch (cmd) {
                case 'ping':
                    callback({ gdprApplies: false, cmpLoaded: true, cmpStatus: 'loaded', displayStatus: 'hidden', apiVersion: '2.2', cmpId: 1, tcfPolicyVersion: 4 }, true);
                    break;
                case 'addEventListener':
                    let id = _callbacks.length;
                    _callbacks.push(callback);
                    callback({ gdprApplies: false, tcString: '', eventStatus: 'tcloaded', listenerId: id, cmpId: 1 }, true);
                    break;
                case 'removeEventListener':
                    break;
            }
        };

        if (!window.frames['__tcfapiLocator']) {
            let f = document.createElement('iframe');
            f.style.cssText = 'display:none';
            f.name = '__tcfapiLocator';
            (document.body || document.documentElement).appendChild(f);
        }
    })();

    function setCookieConsent(value) {
        let cfg = window.CookieConsent;
        let expires = new Date(Date.now() + cfg.duration * 1000).toUTCString();
        let cookie = cfg.cookieName + '=' + value + '; expires=' + expires + '; path=/; SameSite=' + cfg.sameSite;
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
        let el = document.getElementById('cookie-consent-banner');
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
