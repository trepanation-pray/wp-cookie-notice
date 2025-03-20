function setCookieSettings(cookie, value) {
  console.log(`Cookie set: ${cookie} = ${value}`);
    document.cookie = cookie + "=" + value + "; expires=Thu, 1 Jan 2099 12:00:00 GMT; path=/";
}

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");

  if (parts.length == 2)
    return parts
      .pop()
      .split(";")
      .shift();
}

var stringToHTML = function (str) {
  var parser = new DOMParser();
  var doc = parser.parseFromString(str, 'text/html');
  return doc.body;
};

// Trap tabbing in overly version
function tabTrappingCookieNotice() {
  var overlay = document.body.querySelector('.cookie-control-notice--overlay');
  if (overlay) {
    var cookieNoticeTabList = document.querySelectorAll('.cookie-control-notice__landing a, .cookie-control-notice__landing button');
    
    cookieNoticeTabList[0].focus();
    cookieNoticeTabList[0].blur();

    cookieNoticeTabList[cookieNoticeTabList.length - 1].addEventListener('keydown', (event) => {
      if (event.shiftKey && event.keyCode == 9) {
        event.preventDefault();
        cookieNoticeTabList[cookieNoticeTabList.length - 2].focus();
      } else if (event.keyCode == 9) {
        event.preventDefault();
        cookieNoticeTabList[0].focus();
      }
    }, false);

    cookieNoticeTabList[0].addEventListener('keydown', (event) => {
      if (event.shiftKey && event.keyCode == 9) {
        event.preventDefault();
        cookieNoticeTabList[cookieNoticeTabList.length - 1].focus();
      } else if (event.keyCode == 9) {
        event.preventDefault();
        cookieNoticeTabList[1].focus();
      }
    }, false);
  }
}

function updateConsentStatus(adStorage, analyticsStorage, marketingStorage, functionalityStorage, securityStorage, personalizationStorage) {
  const consentSettings = {
    'ad_storage': adStorage,
    'analytics_storage': analyticsStorage,
    'marketing_storage': marketingStorage,
    'ad_user_data': adStorage,
    'ad_personalization': adStorage, // using advertising consent for ad personalization
    'functionality_storage': functionalityStorage,
    'security_storage': securityStorage,
    'personalization_storage': personalizationStorage
  };

  if (typeof gtag === 'function') {
    gtag('consent', 'update', consentSettings);
  } else {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(function() {
      gtag('consent', 'update', consentSettings);
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
    
  // Read consent values from cookies with defaults (if cookies are not set, default to 'denied')
  const trackingConsent = getCookie('cookieControlTracking') === 'accept' ? 'granted' : 'denied';
  const marketingConsent = getCookie('cookieControlMarketing') === 'accept' ? 'granted' : 'denied';
  const advertisingConsent = getCookie('cookieControlAdvertising') === 'accept' ? 'granted' : 'denied';
  const essentialConsent = getCookie('cookieControlEssential') === 'accept' ? 'granted' : 'denied';
  const securityConsent = getCookie('cookieControlSecurity') === 'accept' ? 'granted' : 'denied';
  const personalizationConsent = getCookie('cookieControlPersonalization') === 'accept' ? 'granted' : 'denied';  

  // Update Google Consent Mode based on stored consent
  updateConsentStatus(
    advertisingConsent,      // ad_storage (advertising)
    trackingConsent,         // analytics_storage
    marketingConsent,        // marketing_storage
    essentialConsent,        // functionality_storage
    securityConsent,         // security_storage
    personalizationConsent   // personalization_storage
  );
    

  // Initialize consent state if not already set
  if (typeof window.wpConsentApi !== 'undefined' && window.wpConsentApi.consents) {
    if (!window.wpConsentApi.consents.get('analytics')) {
      window.wpConsentApi.consents.set('analytics', false);
      window.wpConsentApi.consents.set('functional', false);
      window.wpConsentApi.consents.set('marketing', false);
    }
  }

  // Fetch and display the cookie notice if needed
  if (!getCookie('cookieControlTracking') || !getCookie('cookieControlMarketing') || !getCookie('cookieControlAdvertising') || !getCookie('cookieControlEssential')) {
    fetch('/wp-json/cookie-control/notice-content')
      .then(response => response.text())
      .then(body => {
        var html = stringToHTML(body).querySelector('.cookie-control-notice');
        document.body.prepend(html);
        setTimeout(() => {
          html.classList.add('active');
          var content = document.body.querySelector('.cookie-control-notice__container');
          content.focus();
          tabTrappingCookieNotice();
        }, 100);
      });
  }
  
});

function closeCookieNotice() {
  setTimeout(function () {
    document.body.querySelector(".cookie-control-notice").classList.remove("active");
  }, 1000);
  setTimeout(function () {
    document.body.querySelector(".cookie-control-notice").remove();
  }, 1500);
}

// Accept button event handler: set all cookies to "accept"
document.body.addEventListener("click", function (event) {
  if (!event.target.matches(".cookie-control-notice__button--accept")) return;
  event.preventDefault();
  console.log("Consent button clicked"); // Debug log

  setCookieSettings("cookieControlTracking", "accept");
  setCookieSettings("cookieControlMarketing", "accept");
  setCookieSettings("cookieControlAdvertising", "accept"); // Separate advertising cookie
  setCookieSettings("cookieControlEssential", "accept");
  setCookieSettings("cookieControlSecurity", "accept");
  setCookieSettings("cookieControlPersonalization", "accept");

  if (typeof window.wpConsentApi !== 'undefined' && window.wpConsentApi.consents) {
    window.wpConsentApi.consents.set('analytics', true);
    window.wpConsentApi.consents.set('functional', true);
    window.wpConsentApi.consents.set('marketing', true);
  }

  updateConsentStatus('granted', 'granted', 'granted', 'granted', 'granted', 'granted');
  closeCookieNotice();
}, false);

document.body.addEventListener("click", function (event) {
  if (!event.target.matches(".cookie-control-notice__button--manage")) return;
  event.preventDefault();

  var cookieControlLanding = document.querySelector(".cookie-control-notice__landing");
  var cookieControlManage = document.querySelector(".cookie-control-notice__manage");

  cookieControlLanding.style.display = 'none';
  cookieControlManage.style.display = 'block';
}, false);


// Reject button event handler: set all cookies to "reject"
document.body.addEventListener("click", function (event) {
  if (!event.target.matches(".cookie-control-notice__button--reject")) return;
  event.preventDefault();

  setCookieSettings("cookieControlTracking", "reject");
  setCookieSettings("cookieControlMarketing", "reject");
  setCookieSettings("cookieControlAdvertising", "reject"); // Separate advertising cookie
  setCookieSettings("cookieControlEssential", "reject");
  setCookieSettings("cookieControlSecurity", "reject");
  setCookieSettings("cookieControlPersonalization", "reject");

  if (typeof window.wpConsentApi !== 'undefined' && window.wpConsentApi.consents) {
    window.wpConsentApi.consents.set('analytics', false);
    window.wpConsentApi.consents.set('functional', false);
    window.wpConsentApi.consents.set('marketing', false);
  }

  updateConsentStatus('denied', 'denied', 'denied', 'denied', 'denied', 'denied');
  closeCookieNotice();
}, false);


// Save button event handler (for manual consent management)
document.body.addEventListener("click", function (event) {
  if (!event.target.matches(".cookie-control-save-button")) return;
  event.preventDefault();
  event.target.classList.add('loading');

  // Retrieve user selections with a fallback to 'reject'
  var cookieControlTrackingValue = document.querySelector("[name=tracking-cookies]:checked")?.value || 'reject';
  var cookieControlMarketingValue = document.querySelector("[name=marketing-cookies]:checked")?.value || 'reject';
  var cookieControlAdvertisingValue = document.querySelector("[name=advertising-cookies]:checked")?.value || 'reject';
  var cookieControlEssentialValue = document.querySelector("[name=essential-cookies]:checked")?.value || 'reject';
  var cookieControlSecurityValue = document.querySelector("[name=security-cookies]:checked")?.value || 'reject';
  var cookieControlPersonalizationValue = document.querySelector("[name=personalization-cookies]:checked")?.value || 'reject';

  console.log("Tracking Cookie Value:", cookieControlTrackingValue);
  console.log("Marketing Cookie Value:", cookieControlMarketingValue);
  console.log("Advertising Cookie Value:", cookieControlAdvertisingValue);
  console.log("Essential Cookie Value:", cookieControlEssentialValue);
  console.log("Security Cookie Value:", cookieControlSecurityValue);
  console.log("Personalization Cookie Value:", cookieControlPersonalizationValue);

  // Set cookie values accordingly
  setCookieSettings("cookieControlTracking", cookieControlTrackingValue);
  setCookieSettings("cookieControlMarketing", cookieControlMarketingValue);
  setCookieSettings("cookieControlAdvertising", cookieControlAdvertisingValue);
  setCookieSettings("cookieControlEssential", cookieControlEssentialValue);
  setCookieSettings("cookieControlSecurity", cookieControlSecurityValue);
  setCookieSettings("cookieControlPersonalization", cookieControlPersonalizationValue);

  // Define consent statuses based on user selections
  const adStorage = cookieControlAdvertisingValue === 'accept' ? 'granted' : 'denied';
  const analyticsStorage = cookieControlTrackingValue === 'accept' ? 'granted' : 'denied';
  const marketingStorage = cookieControlMarketingValue === 'accept' ? 'granted' : 'denied';
  const functionalityStorage = cookieControlEssentialValue === 'accept' ? 'granted' : 'denied';
  const securityStorage = cookieControlSecurityValue === 'accept' ? 'granted' : 'denied';
  const personalizationStorage = cookieControlPersonalizationValue === 'accept' ? 'granted' : 'denied';

  // Update Google Consent Mode with the chosen settings
  updateConsentStatus(adStorage, analyticsStorage, marketingStorage, functionalityStorage, securityStorage, personalizationStorage);

  // Reload page to apply changes
  window.location.assign(window.location.pathname.slice(0, -1));
}, false);



document.body.addEventListener("click", function (event) {
  if (!event.target.matches(".cookie-control-clear-all-button")) return;
  event.preventDefault();
  event.target.classList.add('loading');
  fetch('/wp-json/cookie-control/clear-cookies')
    .then(window.location.assign(window.location.pathname.slice(0, -1)));
}, false);
