@if($googleAdsenseClientId)
        <meta name="google-adsense-account" content="ca-{{ $googleAdsenseClientId }}">
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
        <!-- PostHog -->
        <script>
            window.__posthogConfig = {
                token: @js($posthogProjectToken),
                host: @js($posthogHost),
                uiHost: @js($posthogUiHost ?? $posthogHost),
            };
        </script>
        <script>
            !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.crossOrigin="anonymous",p.async=!0,p.src=s.api_host.replace(".i.posthog.com","-assets.i.posthog.com")+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="init capture register register_once register_for_session unregister unregister_for_session getFeatureFlag getFeatureFlagPayload isFeatureEnabled reloadFeatureFlags updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures on onFeatureFlags onSessionId getSurveys getActiveMatchingSurveys renderSurvey canRenderSurvey getNextSurveyStep identify setPersonProperties group resetGroups setPersonPropertiesForFlags resetPersonPropertiesForFlags setGroupPropertiesForFlags resetGroupPropertiesForFlags reset get_distinct_id getGroups get_session_id get_session_replay_url alias set_config startSessionRecording stopSessionRecording sessionRecordingStarted captureException loadToolbar get_property getSessionProperty createPersonProfile opt_in_capturing opt_out_capturing has_opted_in_capturing has_opted_out_capturing clear_opt_in_out_capturing debug".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
            posthog.init(window.__posthogConfig.token, {
                api_host: window.__posthogConfig.host,
                ui_host: window.__posthogConfig.uiHost,
                persistence: 'cookie',
            });
        </script>
@endif
