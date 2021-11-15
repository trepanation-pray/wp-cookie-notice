# Required functions and shortcodes

## Cookie control page shortcodes

Output the tracking cookies options.

`[tracking_cookies]`

Output the essential cookies options.

`[essential_cookies]`

Output save preferences button.

`[save_preferences_button class="CUSTOM-BUTTON-CLASSES"]`

Output clear all cookies button.

`[clear_cookies_button class="CUSTOM-BUTTON-CLASSES"]`

## Template functions

Function to check if cookies are set for either "tracking" or "essential" returns a boolean response accordingly.

`cookie_control("tracking")`

Example usage 

```
// PHP

if(cookie_control("tracking")):
  // Code here only displays if user has accepted tracking cookies.
endif;
```

**Javascript**

*Node dependency*

`npm install @trepanation-pray/cookie`

```
// javascript

import { getCookie, setCookie } from '@trepanation-pray/cookie';

if ( getCookie("cookieControlTracking")) {
  // Code here only displays if user has accepted tracking cookies.
}
```

*Google analytics example*

```
// javascript

// Google analytics

function googleAnalytics() {
  var AnalyticsScript = document.createElement('script');
  var AnalyticsId = 'XX-XXXXXXX';
  AnalyticsScript.onload = () => {
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', AnalyticsId);
  };

  AnalyticsScript.src = 'https://www.googletagmanager.com/gtag/js?id=' + AnalyticsId;

  document.head.appendChild(AnalyticsScript);
}

if (getCookie('cookieControlTracking')) {
  googleAnalytics();
}

document.addEventListener('cookieAccept', function (event) {
  googleAnalytics();
  console.log('accept cookies')
}, false);

document.addEventListener('cookieReject', function (event) {
  console.log('reject cookies')
}, false);


```