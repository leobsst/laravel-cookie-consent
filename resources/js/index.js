// Listen for cookie consent updates
document.addEventListener('livewire:initialized', () => {
    Livewire.on('cookie-consent-updated', (event) => {
        const consent = event.consent;

        // Check if gtag is available (it should be if user already had consent)
        if (typeof gtag === 'function') {
            if (consent === true) {
                // User accepted - grant all consents
                gtag('consent', 'update', {
                    'functional_storage': 'granted',
                    'security_storage': 'granted',
                    'analytics_storage': 'granted',
                    'ad_storage': 'granted',
                    'ad_user_data': 'granted',
                    'ad_personalization': 'granted'
                });
            } else if (consent === false) {
                // User refused - deny all consents
                gtag('consent', 'update', {
                    'functional_storage': 'granted',
                    'security_storage': 'granted',
                    'analytics_storage': 'denied',
                    'ad_storage': 'denied',
                    'ad_user_data': 'denied',
                    'ad_personalization': 'denied'
                });
            }
        } else {
            if (consent === true) {
                window.location.reload();
            }
        }
    });
});