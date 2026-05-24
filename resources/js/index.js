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

    // PostHog init — runs only when the config object is present (set inline by scripts.blade.php)
    if (window.__posthogConfig) {
        !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.crossOrigin="anonymous",p.async=!0,p.src=s.api_host.replace(".i.posthog.com","-assets.i.posthog.com")+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="init capture register register_once register_for_session unregister unregister_for_session getFeatureFlag getFeatureFlagPayload isFeatureEnabled reloadFeatureFlags updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures on onFeatureFlags onSessionId getSurveys getActiveMatchingSurveys renderSurvey canRenderSurvey getNextSurveyStep identify setPersonProperties group resetGroups setPersonPropertiesForFlags resetPersonPropertiesForFlags setGroupPropertiesForFlags resetGroupPropertiesForFlags reset get_distinct_id getGroups get_session_id get_session_replay_url alias set_config startSessionRecording stopSessionRecording sessionRecordingStarted captureException loadToolbar get_property getSessionProperty createPersonProfile opt_in_capturing opt_out_capturing has_opted_in_capturing has_opted_out_capturing clear_opt_in_out_capturing debug".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
        posthog.init(window.__posthogConfig.token, {
            api_host: window.__posthogConfig.host,
            ui_host: window.__posthogConfig.uiHost,
            persistence: 'cookie',
        });
    }
})();
