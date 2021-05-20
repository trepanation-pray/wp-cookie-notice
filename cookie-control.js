
var cookieNoticeElement = document.querySelector(".cookie-control-notice");

if (cookieNoticeElement) {
  cookieNoticeElement.classList.add("active");
}

function setCookieSettings(cookie, value) {
  document.cookie = cookie + "=" + value + "; expires=Thu, 1 Jan 2099 12:00:00 GMT; path=/";
}

function closeCookieNotice() {
  cookieNoticeElement.classList.remove("active");
  setTimeout(function () {
    cookieNoticeElement.parentNode.parentNode.removeChild(cookieNoticeElement);
  }, 500);
}

document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-notice__button--accept")) return;
  event.preventDefault();
  setCookieSettings("cookieControlTracking", "accept");
  setCookieSettings("cookieControlEssential", "accept");
  closeCookieNotice();
  window.location.reload()

}, false);

document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-notice__button--reject")) return;
  event.preventDefault();
  setCookieSettings("cookieControlTracking", "reject");
  setCookieSettings("cookieControlEssential", "reject");
  closeCookieNotice();
  window.location.reload();

}, false);

document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-save-button")) return;
  event.preventDefault();

  var cookieControlTrackingValue = document.querySelector("[name=tracking-cookies]:checked").value;
  var cookieControlEssentialValue = document.querySelector("[name=essential-cookies]:checked").value;

  setCookieSettings("cookieControlTracking", cookieControlTrackingValue);
  setCookieSettings("cookieControlEssential", cookieControlEssentialValue);
  window.location.assign('/cookies');

}, false);



document.body.addEventListener("click", function (event) {

  if (!event.target.matches(".cookie-control-clear-all-button")) return;
  event.preventDefault();

  fetch('/app/plugins/cookie-control/clear-cookies.php')
    .then(window.location.assign('/cookies'));

}, false);
