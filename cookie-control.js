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

window.addEventListener('load', () => {
  fetch('/app/plugins/wp-cookie-notice/cookie-control-notice.php')
    .then(response => response.text())
    .then(body => {
      if (!getCookie('cookieControlTracking') || !getCookie('cookieControlEssential')) {
        var html = stringToHTML(body).querySelector('.cookie-control-notice')
        setTimeout(() => {
          html.classList.add('active');
        }, 100);
        document.body.prepend(html)
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
  closeCookieNotice();
  document.dispatchEvent(cookieAccept);
}, false);

document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-notice__button--reject")) return;
  event.preventDefault();
  event.target.classList.add('loading');
  setCookieSettings("cookieControlTracking", "reject");
  setCookieSettings("cookieControlEssential", "reject");
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
  window.location.assign(window.location.pathname.slice(0, -1));

}, false);



document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-clear-all-button")) return;
  event.preventDefault();
  event.target.classList.add('loading');
  fetch('/app/plugins/wp-cookie-notice/clear-cookies.php')
    .then(window.location.assign(window.location.pathname.slice(0, -1)));

}, false);


// Trap tabbing in overly version

if (document.querySelector('.cookie-control-notice--overlay')) {

  var cookieNoticeTabList = document.querySelectorAll('.cookie-control-notice a, .cookie-control-notice button');

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