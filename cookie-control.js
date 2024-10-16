function setCookieSettings(cookie, value) {
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
      console.log(1111);
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
function updateConsentStatus(adStorage, analyticsStorage) {
  if (typeof gtag === 'function') {
    gtag('consent', 'update', {
      'ad_storage': adStorage,
      'analytics_storage': analyticsStorage
    });
  } else {
    // If gtag is not yet available, wait for it
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(function() {
      gtag('consent', 'update', {
        'ad_storage': adStorage,
        'analytics_storage': analyticsStorage
      });
    });
  }
}
document.addEventListener('DOMContentLoaded', () => {
  fetch('/wp-json/cookie-control/notice-content')
    .then(response => response.text())
    .then(body => {
      if (!getCookie('cookieControlTracking') || !getCookie('cookieControlEssential')) {
        var html = stringToHTML(body).querySelector('.cookie-control-notice')
        document.body.prepend(html);
        setTimeout(() => {
          html.classList.add('active');
          var content = document.body.querySelector('.cookie-control-notice__container');
          content.focus();
          tabTrappingCookieNotice();
        }, 100);
      }
    });
});



function closeCookieNotice() {

  setTimeout(function () {
    document.body.querySelector(".cookie-control-notice").classList.remove("active");
  }, 1000);
  setTimeout(function () {
    document.body.querySelector(".cookie-control-notice").remove();
  }, 1500);

}

var cookieAccept = document.createEvent("Event");
cookieAccept.initEvent("cookieAccept", true, true);

var cookieReject = document.createEvent("Event");
cookieReject.initEvent("cookieReject", true, true);


document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-notice__button--accept")) return;
  event.preventDefault();
  event.target.classList.add('loading');
  setCookieSettings("cookieControlTracking", "accept");
  setCookieSettings("cookieControlEssential", "accept");

  // Update Google Consent Mode
  gtag('consent', 'update', {
    'ad_storage': 'granted',
    'analytics_storage': 'granted'
  });

  closeCookieNotice();
  document.dispatchEvent(cookieAccept);
}, false);

document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-notice__button--manage")) return;
  event.preventDefault();
  
  var cookieControlLanding = document.querySelector(".cookie-control-notice__landing");
  var cookieControlManage = document.querySelector(".cookie-control-notice__manage");

  cookieControlLanding.style.display = 'none';
  cookieControlManage.style.display = 'block';

}, false);

document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-notice__button--reject")) return;
  event.preventDefault();
  event.target.classList.add('loading');
  setCookieSettings("cookieControlTracking", "reject");
  setCookieSettings("cookieControlEssential", "reject");

   // Update Google Consent Mode
   gtag('consent', 'update', {
    'ad_storage': 'denied',
    'analytics_storage': 'denied'
  });

  closeCookieNotice();
  document.dispatchEvent(cookieReject);

}, false);


document.body.addEventListener("click", function (event) {
  if (!event.target.matches(".cookie-control-save-button")) return;
  event.preventDefault();
  event.target.classList.add('loading');

  var cookieControlTrackingValue = document.querySelector("[name=tracking-cookies]:checked").value;
  var cookieControlEssentialValue = document.querySelector("[name=essential-cookies]:checked").value;

  setCookieSettings("cookieControlTracking", cookieControlTrackingValue);
  setCookieSettings("cookieControlEssential", cookieControlEssentialValue);

  // Determine consent status based on user selections
  var adStorage = cookieControlTrackingValue === 'accept' ? 'granted' : 'denied';
  var analyticsStorage = cookieControlTrackingValue === 'accept' ? 'granted' : 'denied';

  // Update Google Consent Mode
  gtag('consent', 'update', {
    'ad_storage': adStorage,
    'analytics_storage': analyticsStorage
  });

  window.location.assign(window.location.pathname.slice(0, -1));
}, false);




document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-clear-all-button")) return;
  event.preventDefault();
  event.target.classList.add('loading');
  fetch('/wp-json/cookie-control/clear-cookies')
    .then(window.location.assign(window.location.pathname.slice(0, -1)));

}, false);
