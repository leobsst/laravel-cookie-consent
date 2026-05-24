@if($googleAdsenseClientId)
        <meta name="google-adsense-account" content="ca-{{ $googleAdsenseClientId }}">
@endif

@if($googleAdsenseClientId || $googleTagManagerId || $posthogProjectToken)
        @once
        <script src="{{ asset('vendor/cookie-consent/cookie-consent.js') }}"></script>
        @endonce
@endif

@if($googleAdsenseClientId && !$googleTagManagerId)
        <!-- Google AdSense with Consent Mode v2 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=ca-{{ $googleAdsenseClientId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('consent', 'default', {
                'ad_storage': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied'),
                'ad_user_data': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied'),
                'ad_personalization': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied'),
            });
            gtag('js', new Date());
            gtag('config', @js('ca-' . $googleAdsenseClientId));
        </script>
@endif

@if($googleTagManagerId)
        <!-- Google Tag Manager -->
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('consent', 'default', {
                'functional_storage': 'granted',
                'security_storage': 'granted',
                'analytics_storage': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied'),
                'ad_storage': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied'),
                'ad_user_data': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied'),
                'ad_personalization': @js($cookieConsentStatus === 'full' ? 'granted' : 'denied')
            });
        </script>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer',@js($googleTagManagerId));
        </script>
        <script>
            gtag('js', new Date());
            gtag('config', @js($googleTagManagerId));
        </script>
@endif

@if($posthogProjectToken)
        <!-- PostHog config — init is handled by cookie-consent.js -->
        <script>
            window.__posthogConfig = {
                token: @js($posthogProjectToken),
                host: @js($posthogHost),
                uiHost: @js($posthogUiHost ?? $posthogHost),
            };
        </script>
@endif
