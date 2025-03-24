/* cookie-control.js */

// --- Unified Cookie Consent JS ---
// This file integrates our unified consent handling (mapping, persistence, gtag updates)
// together with the popup functionality that loads the cookie notice and lets the user
// accept, reject, or manage their preferences.

// Helper: set a cookie (for 365 days)
function setCookie(name, value, days) {
  var d = new Date();
  d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
  var expires = "expires=" + d.toUTCString();
  document.cookie = name + "=" + value + "; " + expires + "; path=/";
}

// Helper: retrieve a cookie value by name
function getCookie(name) {
  var decodedCookies = decodeURIComponent(document.cookie);
  var cookieArray = decodedCookies.split(';');
  for (var i = 0; i < cookieArray.length; i++) {
    var cookie = cookieArray[i].trim();
    if (cookie.indexOf(name + "=") === 0) {
      return cookie.substring((name + "=").length, cookie.length);
    }
  }
  return "";
}

// Update a specific consent setting via gtag and persist in a cookie.
function updateConsent(consentType, consentValue) {
  if (typeof gtag === 'function') {
    gtag('consent', 'update', { [consentType]: consentValue });
  }
  setCookie(consentType, consentValue, 365);
}

// Load saved consent preferences and update the UI radio buttons.
function loadConsentPreferences() {
  var mapping = {
    'analytics_storage': 'tracking-cookies',
    'marketing_storage': 'marketing-cookies',
    'ad_storage': 'advertising-cookies',
    'ad_personalization': 'advertising-cookies',
    'functionality_storage': 'essential-cookies'
  };

  for (var consentKey in mapping) {
    var radioGroupName = mapping[consentKey];
    var storedValue = getCookie(consentKey) || 'denied'; // default to 'denied'
    var radioValue = storedValue === 'granted' ? 'accept' : 'reject';
    var inputElem = document.querySelector('input[name="' + radioGroupName + '"][value="' + radioValue + '"]');
    if (inputElem) {
      inputElem.checked = true;
    }
    updateConsent(consentKey, storedValue);
  }
}

// Convert an HTML string into a DocumentFragment.
function stringToHTML(str) {
  var parser = new DOMParser();
  var doc = parser.parseFromString(str, 'text/html');
  return doc.body;
}

// Trap tabbing within the overlay popup (for accessibility).
function tabTrappingCookieNotice() {
  var overlay = document.body.querySelector('.cookie-control-notice--overlay');
  if (overlay) {
    var cookieNoticeTabList = document.querySelectorAll('.cookie-control-notice__landing a, .cookie-control-notice__landing button');
    if (cookieNoticeTabList.length > 0) {
      cookieNoticeTabList[0].focus();
      cookieNoticeTabList[0].blur();
      cookieNoticeTabList[cookieNoticeTabList.length - 1].addEventListener('keydown', function(event) {
        if (event.shiftKey && event.keyCode === 9) {
          event.preventDefault();
          cookieNoticeTabList[cookieNoticeTabList.length - 2].focus();
        } else if (event.keyCode === 9) {
          event.preventDefault();
          cookieNoticeTabList[0].focus();
        }
      }, false);
      cookieNoticeTabList[0].addEventListener('keydown', function(event) {
        if (event.shiftKey && event.keyCode === 9) {
          event.preventDefault();
          cookieNoticeTabList[cookieNoticeTabList.length - 1].focus();
        } else if (event.keyCode === 9) {
          event.preventDefault();
          cookieNoticeTabList[1].focus();
        }
      }, false);
    }
  }
}

// Close the cookie notice popup.
function closeCookieNotice() {
  setTimeout(function () {
    var notice = document.body.querySelector(".cookie-control-notice");
    if (notice) notice.classList.remove("active");
  }, 1000);
  setTimeout(function () {
    var notice = document.body.querySelector(".cookie-control-notice");
    if (notice) notice.remove();
  }, 1500);
}

document.addEventListener('DOMContentLoaded', function() {
  loadConsentPreferences();

  // Only show the popup if the decision cookie is not set.
  if (!getCookie('cookie_control_decided')) {
    console.log("Cookie notice not decided yet. Loading popup...");
    fetch('/wp-json/cookie-control/notice-content')
      .then(response => response.text())
      .then(body => {
        console.log("Fetched body:", body);
        var parsed = stringToHTML(body);
        console.log("Parsed HTML:", parsed);
        var noticeElement = parsed.querySelector('.cookie-control-notice');
        if (noticeElement) {
          console.log("Found notice element:", noticeElement);
          try {
            document.body.prepend(noticeElement);
            console.log("Popup element prepended to body.");
          } catch(e) {
            console.log("Prepend failed, attempting appendChild:", e);
            document.body.appendChild(noticeElement);
          }
          setTimeout(function() {
            noticeElement.classList.add('active');
            var content = document.body.querySelector('.cookie-control-notice__container');
            if (content) content.focus();
            tabTrappingCookieNotice();
          }, 100);
        } else {
          console.log("No element with class 'cookie-control-notice' found in the fetched HTML.");
        }
      })
      .catch(function(err) {
        console.error("Error fetching cookie notice:", err);
      });
  } else {
    console.log("Cookie notice already decided.");
  }

  // "Save preferences" button event listener.
  var saveButton = document.querySelector('.cookie-control-save-button');
  if (saveButton) {
    saveButton.addEventListener('click', function() {
      var trackingConsent = document.querySelector('input[name="tracking-cookies"]:checked');
      var marketingConsent = document.querySelector('input[name="marketing-cookies"]:checked');
      var advertisingConsent = document.querySelector('input[name="advertising-cookies"]:checked');
      var essentialConsent = document.querySelector('input[name="essential-cookies"]:checked');

      updateConsent('analytics_storage', (trackingConsent && trackingConsent.value === 'accept') ? 'granted' : 'denied');
      updateConsent('marketing_storage', (marketingConsent && marketingConsent.value === 'accept') ? 'granted' : 'denied');
      var advStatus = (advertisingConsent && advertisingConsent.value === 'accept') ? 'granted' : 'denied';
      updateConsent('ad_storage', advStatus);
      updateConsent('ad_personalization', advStatus);
      updateConsent('functionality_storage', (essentialConsent && essentialConsent.value === 'accept') ? 'granted' : 'denied');

      // Mark that the user has decided.
      setCookie('cookie_control_decided', 'true', 365);
      closeCookieNotice();
      // No page reload – changes are applied on the same page.
    });
  }

  // "Clear all cookies" button event listener.
  var clearButton = document.querySelector('.cookie-control-clear-all-button');
  if (clearButton) {
    clearButton.addEventListener('click', function() {
      var consentKeys = ['analytics_storage', 'marketing_storage', 'ad_storage', 'ad_personalization', 'functionality_storage', 'cookie_control_decided'];
      for (var i = 0; i < consentKeys.length; i++) {
        document.cookie = consentKeys[i] + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
      }
      // Instead of reloading the page, you might choose to show the popup again.
      closeCookieNotice();
    });
  }

  // Accept button event handler: set all consent cookies to "granted."
  document.body.addEventListener("click", function (event) {
    if (!event.target.matches(".cookie-control-notice__button--accept")) return;
    event.preventDefault();
    console.log("Consent accept button clicked");

    updateConsent('analytics_storage', 'granted');
    updateConsent('marketing_storage', 'granted');
    updateConsent('ad_storage', 'granted');
    updateConsent('ad_personalization', 'granted');
    updateConsent('functionality_storage', 'granted');
    updateConsent('security_storage', 'granted');
    updateConsent('personalization_storage', 'granted');

    setCookie('cookie_control_decided', 'true', 365);

    if (typeof window.wpConsentApi !== 'undefined' && window.wpConsentApi.consents) {
      window.wpConsentApi.consents.set('analytics', true);
      window.wpConsentApi.consents.set('functional', true);
      window.wpConsentApi.consents.set('marketing', true);
    }

    closeCookieNotice();
    // No page reload.
  }, false);

  // Reject button event handler: set all consent cookies to "denied."
  document.body.addEventListener("click", function (event) {
    if (!event.target.matches(".cookie-control-notice__button--reject")) return;
    event.preventDefault();

    updateConsent('analytics_storage', 'denied');
    updateConsent('marketing_storage', 'denied');
    updateConsent('ad_storage', 'denied');
    updateConsent('ad_personalization', 'denied');
    updateConsent('functionality_storage', 'denied');
    updateConsent('security_storage', 'denied');
    updateConsent('personalization_storage', 'denied');

    setCookie('cookie_control_decided', 'true', 365);

    if (typeof window.wpConsentApi !== 'undefined' && window.wpConsentApi.consents) {
      window.wpConsentApi.consents.set('analytics', false);
      window.wpConsentApi.consents.set('functional', false);
      window.wpConsentApi.consents.set('marketing', false);
    }

    closeCookieNotice();
    // No page reload.
  }, false);

  // Manage button event handler: toggle display between landing and manage sections.
  document.body.addEventListener("click", function (event) {
    if (!event.target.matches(".cookie-control-notice__button--manage")) return;
    event.preventDefault();

    var cookieControlLanding = document.querySelector(".cookie-control-notice__landing");
    var cookieControlManage = document.querySelector(".cookie-control-notice__manage");

    if (cookieControlLanding && cookieControlManage) {
      cookieControlLanding.style.display = 'none';
      cookieControlManage.style.display = 'block';
    }
  }, false);
});
