(function () {
    // IAB TCF 2.0 stub — prevents Google AdSense from showing its own consent banner.
    // Must run before any async ad script; loaded via a synchronous <script> tag in scripts.blade.php.
    (function () {
        let _listeners = [];

        function _cookieValue() {
            let name = (window.CookieConsent && window.CookieConsent.cookieName) || 'cookie_consent';
            let escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            let match = document.cookie.match(new RegExp('(?:^|;)\\s*' + escaped + '=([^;]*)'));
            return match ? match[1] : null;
        }

        function _readConsent() { return _cookieValue() === 'full'; }
        function _hasDecision() { return _cookieValue() !== null; }

        function _buildTCData(granted, eventStatus) {
            let p = {};
            for (let i = 1; i <= 10; i++) p[i] = granted;
            return {
                tcString: '', tcfPolicyVersion: 4, cmpId: 1, cmpVersion: 1,
                gdprApplies: true, isServiceSpecific: true,
                eventStatus: eventStatus || 'tcloaded',
                purpose: { consents: p, legitimateInterests: {} },
                vendor: { consents: {}, legitimateInterests: {} },
                specialFeatureOptins: {},
                publisher: { consents: p, legitimateInterests: {}, customPurpose: { consents: {}, legitimateInterests: {} }, restrictions: {} },
            };
        }

        window.__tcfapi = function (cmd, version, callback, param) {
            let consent = _readConsent();
            let decided = _hasDecision();
            switch (cmd) {
                case 'ping':
                    // 'visible' while our banner is shown (no decision yet) — tells AdSense a CMP is active so it won't show its own banner.
                    // 'hidden' once the user has decided — tells AdSense consent was handled.
                    callback({ gdprApplies: true, cmpLoaded: true, cmpStatus: 'loaded', displayStatus: decided ? 'hidden' : 'visible', apiVersion: '2.2', cmpId: 1, tcfPolicyVersion: 4 }, true);
                    break;
                case 'getTCData':
                    callback(_buildTCData(consent, decided ? 'useractioncomplete' : 'tcloaded'), true);
                    break;
                case 'addEventListener':
                    let id = _listeners.length;
                    _listeners.push(callback);
                    // 'useractioncomplete' when consent is already known (page reload after decision) — AdSense will serve ads immediately.
                    // 'tcloaded' when no decision yet — AdSense waits for _notify().
                    callback(Object.assign(_buildTCData(consent, decided ? 'useractioncomplete' : 'tcloaded'), { listenerId: id }), true);
                    break;
                case 'removeEventListener':
                    if (param !== undefined && _listeners[param]) { _listeners[param] = null; callback(true, true); }
                    break;
            }
        };

        window.__tcfapi._notify = function (granted) {
            let data = _buildTCData(granted, 'useractioncomplete');
            _listeners.forEach(function (cb, i) { if (cb) cb(Object.assign({}, data, { listenerId: i }), true); });
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
        if (window.__tcfapi && window.__tcfapi._notify) {
            window.__tcfapi._notify(granted);
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
