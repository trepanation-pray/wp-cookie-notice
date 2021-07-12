# Required functions and shortcodes

## Cookie control page shortcodes

You are required to have root page with the slug `cookies`

Output the tracking cookies options.

`[tracking_cookies]`

Output the essential cookies options.

`[essential_cookies]`

Output save preferences button.

`[save_preferences_button class="CUSTOM-BUTTON-CLASSES"]`

Output clear all cookies button.

`[clear_cookies_button class="CUSTOM-BUTTON-CLASSES]`

## Template functions

Output the cookie notice in the template. Place just after "skip to content".

`cookie_control_notice()`

Function to check if cookies are set for either "tracking" or "essential" returns a boolean response accordingly.

`cookie_control("tracking")`

Example usage 

**PHP**
```
if(cookie_control("tracking")):
  // Code here only displays if user has accepted tracking cookies.
endif;
```

**Javascript**

*Node dependency*

`npm install @trepanation-pray/cookie`

```
import { getCookie, setCookie } from '@trepanation-pray/cookie';

if ( getCookie("tracking")) {
  // Code here only displays if user has accepted tracking cookies.
}
```