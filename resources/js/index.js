(function () {
    // TCF 2.0 stub — signals to AdSense that a CMP is active (prevents Funding Choices from loading).
    // Uses pre-encoded TC strings so AdSense respects the consent state without cryptographic validation errors.
    // TC strings source: IAB TCF reference encoder for "all denied" and "all granted" with gdprApplies: true.
    (function () {
        let _callbacks = [];

        // Pre-encoded TC strings for the two possible consent states.
        // These are minimal valid TCFv2 strings that pass AdSense's format check.
        let _tcStrings = {
            granted: 'CAKIf6AAKIf6AABABAFRABEgAP___wAAAAqIAAAAAAAA',
            denied:  'CAKIf6AAKIf6AABABAFRABEgAAAAAAAAAAqIAAAAAAAA',
        };

        function _cookieValue() {
            let name = (window.CookieConsent && window.CookieConsent.cookieName) || 'cookie_consent';
            let escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            let match = document.cookie.match(new RegExp('(?:^|;)\\s*' + escaped + '=([^;]*)'));
            return match ? match[1] : null;
        }

        function _isGranted() { return _cookieValue() === 'full'; }
        function _hasDecision() { return _cookieValue() !== null; }

        function _buildTCData(eventStatus) {
            let granted = _isGranted();
            let p = {};
            for (let i = 1; i <= 10; i++) p[i] = granted;
            let v = {};
            if (granted) {
                [755, 56, 21, 91, 128, 253, 256, 410].forEach(function (id) { v[id] = true; });
            }
            return {
                tcString: granted ? _tcStrings.granted : _tcStrings.denied,
                tcfPolicyVersion: 2, cmpId: 1, cmpVersion: 1,
                gdprApplies: true, isServiceSpecific: true,
                eventStatus: eventStatus,
                purpose: { consents: p, legitimateInterests: {} },
                vendor: { consents: v, legitimateInterests: {} },
                specialFeatureOptins: {},
                publisher: { consents: p, legitimateInterests: {}, customPurpose: { consents: {}, legitimateInterests: {} }, restrictions: {} },
            };
        }

        window.__tcfapi = function (cmd, version, callback, param) {
            let decided = _hasDecision();
            switch (cmd) {
                case 'ping':
                    callback({ gdprApplies: true, cmpLoaded: true, cmpStatus: 'loaded', displayStatus: decided ? 'hidden' : 'visible', apiVersion: '2.2', cmpId: 1, tcfPolicyVersion: 2 }, true);
                    break;
                case 'getTCData':
                    callback(_buildTCData(decided ? 'useractioncomplete' : 'tcloaded'), true);
                    break;
                case 'addEventListener':
                    let id = _callbacks.length;
                    _callbacks.push(callback);
                    callback(Object.assign(_buildTCData(decided ? 'useractioncomplete' : 'tcloaded'), { listenerId: id }), true);
                    break;
                case 'removeEventListener':
                    if (param !== undefined && _callbacks[param]) { _callbacks[param] = null; callback(true, true); }
                    break;
            }
        };

        window.__tcfapi._notify = function () {
            let decided = _hasDecision();
            let data = _buildTCData('useractioncomplete');
            _callbacks.forEach(function (cb, i) { if (cb) cb(Object.assign({}, data, { listenerId: i }), true); });
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
            window.__tcfapi._notify();
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
