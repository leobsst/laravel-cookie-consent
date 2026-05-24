(function () {
    // IAB TCF 2.0 stub — prevents Google AdSense from showing its own consent banner.
    // Must run before any async ad script; loaded via a synchronous <script> tag in scripts.blade.php.
    (function () {
        var _listeners = [];

        function _readConsent() {
            var name = (window.CookieConsent && window.CookieConsent.cookieName) || 'cookie_consent';
            var escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var match = document.cookie.match(new RegExp('(?:^|;)\\s*' + escaped + '=([^;]*)'));
            return match ? match[1] === 'full' : false;
        }

        function _buildTCData(granted, eventStatus) {
            var p = {};
            for (var i = 1; i <= 10; i++) p[i] = granted;
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
            var consent = _readConsent();
            switch (cmd) {
                case 'ping':
                    callback({ gdprApplies: true, cmpLoaded: true, cmpStatus: 'loaded', displayStatus: 'hidden', apiVersion: '2.2', cmpId: 1, tcfPolicyVersion: 4 }, true);
                    break;
                case 'getTCData':
                    callback(_buildTCData(consent), true);
                    break;
                case 'addEventListener':
                    var id = _listeners.length;
                    _listeners.push(callback);
                    callback(Object.assign(_buildTCData(consent), { listenerId: id }), true);
                    break;
                case 'removeEventListener':
                    if (param !== undefined && _listeners[param]) { _listeners[param] = null; callback(true, true); }
                    break;
            }
        };

        window.__tcfapi._notify = function (granted) {
            var data = _buildTCData(granted, 'useractioncomplete');
            _listeners.forEach(function (cb, i) { if (cb) cb(Object.assign({}, data, { listenerId: i }), true); });
        };

        if (!window.frames['__tcfapiLocator']) {
            var f = document.createElement('iframe');
            f.style.cssText = 'display:none';
            f.name = '__tcfapiLocator';
            (document.body || document.documentElement).appendChild(f);
        }
    })();

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
