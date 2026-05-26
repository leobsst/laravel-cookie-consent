import { TCModel, TCString, GVL } from '@iabtcf/core';

// ---------------------------------------------------------------------------
// GVL inline — minimal vendor list required by @iabtcf/core to encode TC strings.
// Only includes vendors relevant to this setup (Google 755 + common ad vendors).
// ---------------------------------------------------------------------------
const GVL_DATA = {
    gvlSpecificationVersion: 3,
    vendorListVersion: 1,
    tcfPolicyVersion: 4,
    lastUpdated: '2024-01-01T00:00:00Z',
    purposes: {
        '1': { id: 1, name: 'Store and/or access information on a device' },
        '2': { id: 2, name: 'Use limited data to select advertising' },
        '3': { id: 3, name: 'Create profiles for personalised advertising' },
        '4': { id: 4, name: 'Use profiles to select personalised advertising' },
        '5': { id: 5, name: 'Use profiles to select personalised content' },
        '6': { id: 6, name: 'Measure advertising performance' },
        '7': { id: 7, name: 'Measure content performance' },
        '8': { id: 8, name: 'Apply market research' },
        '9': { id: 9, name: 'Develop and improve services' },
        '10': { id: 10, name: 'Use limited data to select content' },
    },
    specialPurposes: {
        '1': { id: 1, name: 'Ensure security, prevent and detect fraud, and fix errors' },
        '2': { id: 2, name: 'Deliver and present advertising and content' },
    },
    features: {
        '1': { id: 1, name: 'Match and combine data from other data sources' },
        '2': { id: 2, name: 'Link different devices' },
    },
    specialFeatures: {},
    stacks: {},
    vendors: {
        '21': { id: 21, name: 'Quantcast', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '56': { id: 56, name: 'Criteo', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '91': { id: 91, name: 'Unruly', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '128': { id: 128, name: 'Yieldmo', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '253': { id: 253, name: 'Yieldlab', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '256': { id: 256, name: 'Taboola', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '410': { id: 410, name: 'Digital Turbine', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [], features: [], specialFeatures: [], policyUrl: '', usesCookies: true, cookieMaxAgeSeconds: 86400, cookieRefresh: false, usesNonCookieAccess: false, deviceStorageDisclosureUrl: '' },
        '755': { id: 755, name: 'Google Advertising Products', purposes: [1,2,3,4,5,6,7,8,9,10], legIntPurposes: [], flexiblePurposes: [], specialPurposes: [1,2], features: [1,2], specialFeatures: [], policyUrl: 'https://policies.google.com/privacy', usesCookies: true, cookieMaxAgeSeconds: 34164000, cookieRefresh: false, usesNonCookieAccess: true, deviceStorageDisclosureUrl: '' },
    },
    dataCategories: {},
};

const AD_VENDOR_IDS = [21, 56, 91, 128, 253, 256, 410, 755];
const PURPOSE_IDS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
const CMP_ID = 28;

function buildTCString(granted) {
    const gvl = new GVL(GVL_DATA);
    const model = new TCModel(gvl);
    model.cmpId = CMP_ID;
    model.cmpVersion = 1;
    model.consentScreen = 0;
    model.consentLanguage = 'FR';
    model.isServiceSpecific = true;
    model.gdprApplies = true;
    if (granted) {
        model.purposeConsents.set(PURPOSE_IDS);
        model.vendorConsents.set(AD_VENDOR_IDS);
    }
    return TCString.encode(model);
}

// Build both strings once at load time (synchronous, < 1ms).
const TC_STRINGS = {
    granted: buildTCString(true),
    denied: buildTCString(false),
};

// ---------------------------------------------------------------------------
// TCF 2.2 stub + Google Consent Mode v2
// ---------------------------------------------------------------------------
(function () {
    let _listeners = [];

    function _cookieValue() {
        let name = (window.CookieConsent && window.CookieConsent.cookieName) || 'cookie_consent';
        let escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        let match = document.cookie.match(new RegExp('(?:^|;)\\s*' + escaped + '=([^;]*)'));
        return match ? match[1] : null;
    }

    function _isGranted()   { return _cookieValue() === 'full'; }
    function _hasDecision() { return _cookieValue() !== null; }

    function _buildTCData(eventStatus) {
        let granted = _isGranted();
        let p = {};
        for (let i = 1; i <= 10; i++) p[i] = granted;
        let v = {};
        if (granted) {
            AD_VENDOR_IDS.forEach(function (id) { v[id] = true; });
        }
        return {
            tcString: granted ? TC_STRINGS.granted : TC_STRINGS.denied,
            tcfPolicyVersion: 4,
            cmpId: CMP_ID,
            cmpVersion: 1,
            gdprApplies: true,
            isServiceSpecific: true,
            eventStatus: eventStatus,
            cmpStatus: 'loaded',
            displayStatus: 'hidden',
            addtlConsent: '1~',
            purpose: { consents: p, legitimateInterests: {} },
            vendor: { consents: v, legitimateInterests: {} },
            specialFeatureOptins: {},
            publisher: {
                consents: p,
                legitimateInterests: {},
                customPurpose: { consents: {}, legitimateInterests: {} },
                restrictions: {},
            },
        };
    }

    window.__tcfapi = function (cmd, version, callback, param) {
        let decided = _hasDecision();
        switch (cmd) {
            case 'ping':
                callback({
                    gdprApplies: true,
                    cmpLoaded: true,
                    cmpStatus: 'loaded',
                    displayStatus: 'hidden',
                    apiVersion: '2.2',
                    cmpId: CMP_ID,
                    tcfPolicyVersion: 4,
                }, true);
                break;
            case 'getTCData':
                callback(_buildTCData(decided ? 'useractioncomplete' : 'tcloaded'), true);
                break;
            case 'addEventListener':
                let id = _listeners.length;
                _listeners.push(callback);
                callback(Object.assign(_buildTCData(decided ? 'useractioncomplete' : 'tcloaded'), { listenerId: id }), true);
                break;
            case 'removeEventListener':
                if (param !== undefined && _listeners[param]) {
                    _listeners[param] = null;
                    callback(true, true);
                }
                break;
        }
    };

    window.__tcfapi._notify = function () {
        let data = _buildTCData('useractioncomplete');
        _listeners.forEach(function (cb, i) {
            if (cb) cb(Object.assign({}, data, { listenerId: i }), true);
        });
    };

    if (!window.frames['__tcfapiLocator']) {
        let f = document.createElement('iframe');
        f.style.cssText = 'display:none';
        f.name = '__tcfapiLocator';
        (document.body || document.documentElement).appendChild(f);
    }
})();

// ---------------------------------------------------------------------------
// Cookie helpers
// ---------------------------------------------------------------------------
function setCookieConsent(value) {
    let cfg = window.CookieConsent;
    let expires = new Date(Date.now() + cfg.duration * 1000).toUTCString();
    let cookie = cfg.cookieName + '=' + value + '; expires=' + expires + '; path=/; SameSite=' + cfg.sameSite;
    if (cfg.secure) cookie += '; Secure';
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

function refreshAdSenseSlots() {
    let slots = document.querySelectorAll('ins.adsbygoogle');
    if (!slots.length) return;
    // Make slots visible first so AdSense can measure availableWidth before push.
    setAdSenseSlotsVisibility(true);
    requestAnimationFrame(function () {
        slots.forEach(function (ins) {
            ins.removeAttribute('data-adsbygoogle-status');
            ins.removeAttribute('data-ad-status');
            ins.innerHTML = '';
        });
        slots.forEach(function () {
            (window.adsbygoogle = window.adsbygoogle || []).push({});
        });
    });
}

function setAdSenseSlotsVisibility(visible) {
    if (visible) {
        let style = document.getElementById('adsense-consent-hide');
        if (style) style.remove();
    }
    document.querySelectorAll('ins.adsbygoogle').forEach(function (ins) {
        let target = ins.closest('.adsense-wrapper') || ins;
        target.style.display = visible ? '' : 'none';
    });
}

function updatePosthog(granted) {
    if (!window.__posthogConfig) return;
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
    if (el) el.style.display = 'none';
}

window.__cookieConsent = {
    accept: function () {
        setCookieConsent('full');
        updateGtag(true);
        updatePosthog(true);
        hideBanner();
        setAdSenseSlotsVisibility(true);
        refreshAdSenseSlots();
    },
    refuse: function () {
        setCookieConsent('none');
        updateGtag(false);
        updatePosthog(false);
        hideBanner();
        setAdSenseSlotsVisibility(false);
    },
};

// On page load: inject a stylesheet synchronously to hide ad slots before AdSense pushes them,
// if consent has not been granted. This runs before DOMContentLoaded so AdSense never
// measures availableWidth=0 on a slot that was shown then hidden too late.
(function () {
    let name = (window.CookieConsent && window.CookieConsent.cookieName) || 'cookie_consent';
    let escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    let match = document.cookie.match(new RegExp('(?:^|;)\\s*' + escaped + '=([^;]*)'));
    let granted = match && match[1] === 'full';
    if (!granted) {
        let style = document.createElement('style');
        style.id = 'adsense-consent-hide';
        style.textContent = 'ins.adsbygoogle { display: none !important; }';
        (document.head || document.documentElement).appendChild(style);
    }
}());
