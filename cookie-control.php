<?php
/*
  Plugin Name: Cookie Control
  Description: EU compliant cookie control
  Version: 3.0.9
  Author: Steven Hill
  Author URI: http://www.stevenhill.me
  License: GPL2
*/

function insert_gtm_head_script() {
  $gtm_id = get_option('cookie_control_gtm_id');
  if (!$gtm_id) {
      return;
  }
  ?>
  <!-- Google Tag Manager -->
  <script>
    (function(w,d,s,l,i){
      w[l]=w[l]||[];
      w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
      var f = d.getElementsByTagName(s)[0],
          j = d.createElement(s),
          dl = l!='dataLayer'?'&l='+l:'';
      j.async = true;
      j.src = 'https://www.googletagmanager.com/gtm.js?id='+i+dl;
      f.parentNode.insertBefore(j, f);
    })(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');
  </script>
  <!-- End Google Tag Manager -->
  <?php
}
add_action('wp_head', 'insert_gtm_head_script', 1);

function insert_gtm_body_noscript() {
  $gtm_id = get_option('cookie_control_gtm_id');
  if (!$gtm_id) {
      return;
  }
  ?>
  <!-- Google Tag Manager (noscript) -->
  <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe>
  </noscript>
  <!-- End Google Tag Manager (noscript) -->
  <?php
}
add_action('wp_body_open', 'insert_gtm_body_noscript');



function insert_consent_mode_script() {
  // Determine consent status for each category.
  // If the user has consented, these cookies would be set, otherwise default to 'denied'.
  $trackingConsent    = isset($_COOKIE['cookieControlTracking']) && $_COOKIE['cookieControlTracking'] == 'accept' ? 'granted' : 'denied';
  $marketingConsent   = isset($_COOKIE['cookieControlMarketing']) && $_COOKIE['cookieControlMarketing'] == 'accept' ? 'granted' : 'denied';
  $advertisingConsent = isset($_COOKIE['cookieControlAdvertising']) && $_COOKIE['cookieControlAdvertising'] == 'accept' ? 'granted' : 'denied';
  $essentialConsent   = isset($_COOKIE['cookieControlEssential']) && $_COOKIE['cookieControlEssential'] == 'accept' ? 'granted' : 'denied';
 
  ?>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
 
    // Set the default consent settings (before the user has interacted with the consent dialog).
    gtag('consent', 'default', {
      'ad_storage': 'denied',            // Default: denied for advertising cookies
      'ad_personalization': 'denied',    // Default: denied for personalized ads
      'ad_user_data': 'denied',          // Default: denied for ad user data
      'analytics_storage': 'denied',     // Default: denied for analytics cookies
      'marketing_storage': 'denied',     // Default: denied for marketing cookies
      'security_storage': 'denied',      // Default: denied for security cookies (you may want to enable this)
      'personalization_storage': 'denied' // Default: denied for personalization cookies (you may want to enable this)
    });
 
    // Update the consent mode based on user consent (if cookies are set).
    gtag('consent', 'update', {
      'ad_storage': '<?php echo esc_js($advertisingConsent); ?>',
      'ad_personalization': '<?php echo esc_js($advertisingConsent); ?>',
      'ad_user_data': '<?php echo esc_js($advertisingConsent); ?>',
      'analytics_storage': '<?php echo esc_js($trackingConsent); ?>',
      'marketing_storage': '<?php echo esc_js($marketingConsent); ?>',
      'security_storage': '<?php echo esc_js($essentialConsent); ?>', // Update security consent if applicable
      'personalization_storage': '<?php echo esc_js($essentialConsent); ?>' // Update personalization consent if applicable
    });
</script>
<?php
}
add_action('wp_head', 'insert_consent_mode_script', 1);





function replace_page_content($content) {
  // Check if it's the page with the specified ID (e.g., 123).
  $options = get_option( 'cookie_control_settings_settings');

  if ( is_page($options['cookie_control_page']) ) {
      $cookie_control_text = $options['cookie_control_content'];
      $content = '<div class="cookie-control-notice__content">' . wpautop($cookie_control_text) . '</div>';
  }
  return $content;
}

add_filter('the_content', 'replace_page_content', 10);



function add_content_filter() {
  add_filter('the_content', 'replace_page_content');
}


add_action('init', 'add_content_filter');


add_action( 'admin_menu', 'cookie_control_settings_add_admin_menu' );
add_action( 'admin_init', 'cookie_control_settings_settings_init' );




function cookie_control_settings_add_admin_menu(  ) {

  add_options_page( 'Cookie Control', 'Cookie Control', 'manage_options', 'cookie_control', 'cookie_control_settings_options_page' );

}


function cookie_control_settings_settings_init() {

  // Register the existing settings array.
  register_setting( 'pluginPage', 'cookie_control_settings_settings' );

  // Register the GTM ID as a separate option.
  register_setting('pluginPage', 'cookie_control_gtm_id');

  add_settings_section(
    'cookie_control_settings_pluginPage_section',
    __( '<h1>Cookie Control</h1>', 'Cookie Control Settings' ),
    'cookie_control_settings_settings_section_callback',
    'pluginPage'
  );

  add_settings_field(
    'cookie_control_title',
    __( 'Title', 'Cookie Control Settings' ),
    'cookie_control_title_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_control_description',
    __( 'Description text', 'Cookie Control Settings' ),
    'cookie_control_description_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_control_gtm_id',
    __( 'Google Tag Manager ID', 'Cookie Control Settings' ),
    'cookie_control_gtm_id_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_control_page',
    __( 'Select preferences page', 'Cookie Control Settings' ),
    'cookie_control_page_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );
  add_settings_field(
    'cookie_control_reject_button',
    __( 'Show/Hide reject button', 'Cookie Control Settings' ),
    'cookie_control_reject_button_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );
  add_settings_field(
    'cookie_control_style',
    __( 'Visual style', 'Cookie Control Settings' ),
    'cookie_control_style_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );
  add_settings_field(
    'cookie_control_content',
    __( 'Manage cookie content', 'Cookie Control Settings' ),
    'cookie_control_content_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );

}

function cookie_control_title_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  
  if( isset( $options['cookie_control_title']) ) {
      $cookie_control_title = $options['cookie_control_title'];
    } else {
      $cookie_control_title = 'Cookie Control';
  }
  ?>

  <input type='text' class="regular-text" name='cookie_control_settings_settings[cookie_control_title]' value='<?= $cookie_control_title; ?>'>
  <?php

}
function cookie_control_gtm_id_render() {
  $gtm_id = get_option('cookie_control_gtm_id');
  ?>
  <input type='text' class="regular-text" name='cookie_control_gtm_id' value='<?php echo esc_attr($gtm_id); ?>' placeholder="GTM-XXXXXXX">
  <p class="description"><?php _e('Enter your Google Tag Manager ID (e.g., GTM-W3WWXSP).', 'Cookie Control Settings'); ?></p>
  <?php
}

function cookie_control_page_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  
  if( isset( $options['cookie_control_page']) ) {
      $cookie_control_page = $options['cookie_control_page'];
    } else {
      $cookie_control_page = 0;
  }

  	wp_dropdown_pages(
      array(
        'name'              => 'cookie_control_settings_settings[cookie_control_page]',
        'show_option_none'  => __( '&mdash; Select &mdash;' ),
        'option_none_value' => '0',
        'selected'          => $cookie_control_page,
        'post_status'       => array( 'draft', 'publish' ),
      )
    );
  ?> 
  <?php
}

function cookie_control_style_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  ?>
  <select name="cookie_control_settings_settings[cookie_control_style]" id="cookie_control_style">
    <?php $selected = (isset( $options['cookie_control_style'] ) && $options['cookie_control_style'] === 'overlay') ? 'selected' : '' ; ?>
    <option value="overlay" <?php echo $selected; ?>>Overlay</option>
    <?php $selected = (isset( $options['cookie_control_style'] ) && $options['cookie_control_style'] === 'inline') ? 'selected' : '' ; ?>
    <option value="inline" <?php echo $selected; ?>>Inline</option>
    <?php $selected = (isset( $options['cookie_control_style'] ) && $options['cookie_control_style'] === 'corner') ? 'selected' : '' ; ?>
    <option value="corner" <?php echo $selected; ?>>Corner</option>
     <?php $selected = (isset( $options['cookie_control_style'] ) && $options['cookie_control_style'] === 'none') ? 'selected' : '' ; ?>
    <option value="none" <?php echo $selected; ?>>None</option>
  </select><?php

}

function cookie_control_reject_button_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  ?>
  <select name="cookie_control_settings_settings[cookie_control_reject_button]" id="cookie_control_reject_button">
    <?php $selected = (isset( $options['cookie_control_reject_button'] ) && $options['cookie_control_reject_button'] === 'show') ? 'selected' : '' ; ?>
    <option value="show" <?php echo $selected; ?>>Show</option>
    <?php $selected = (isset( $options['cookie_control_reject_button'] ) && $options['cookie_control_reject_button'] === 'hide') ? 'selected' : '' ; ?>
    <option value="hide" <?php echo $selected; ?>>Hide</option>
  </select><?php

}

function cookie_control_description_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );

    if( isset($options['cookie_control_description']) ) {
      $cookie_control_text = $options['cookie_control_description'];
    } else {
      $cookie_control_text = 'We use some essential cookies to make this website work.'.PHP_EOL.PHP_EOL.'We’d like to set additional cookies to understand how you use the website, remember your settings and improve your services.'.PHP_EOL.PHP_EOL.'We also use cookies set by other sites to help us deliver content from their services.';
    }
  ?>
  <textarea class="regular-text" rows="5" name='cookie_control_settings_settings[cookie_control_description]'><?= $cookie_control_text ?></textarea>
  <?php

}
function cookie_control_content_render(  ) {

    $options = get_option( 'cookie_control_settings_settings' );

    $settings = $settings = [
      'textarea_name' => 'cookie_control_settings_settings[cookie_control_content]',
      'default_editor' => true,
      'media_buttons' => false,
    ];

    if( isset($options['cookie_control_content']) ) {
      $cookie_control_text = $options['cookie_control_content'];
    } else {
      $cookie_control_text = file_get_contents( __DIR__ . '/cookies-page-content.html');
    }
    wp_editor($cookie_control_text, 'cookie_control_content', $settings);
    echo '<p>To use the content above in page where are not using the default editor use this shortcode: <strong>[cookie_notice_page_content]</strong></p>';

}

function cookie_control_settings_settings_section_callback(  ) {

  echo __( '<p>The EU law now imposes that a communications provider must get consent from the user when storing or accessing information. This includes the use of cookies and any form of tracking.</p><p>If no values are set predefined defaults will be used.</p><p>Code and template references can be viewed <a href="https://github.com/trepanation-pray/wp-cookie-notice#readme" target="_blank">here</a></p>', 'Cookie Control Settings' );

}

function cookie_control_settings_options_page(  ) {

  ?>
  <form action='options.php' method='post'>
    <div class="wrap">
      <?php
      settings_fields( 'pluginPage' );
      do_settings_sections( 'pluginPage' );
      submit_button();
      ?>
    </div>
  </form>
  <?php

}

add_action('wp_enqueue_scripts', 'cookie_control_assets');

function cookie_control_assets() {
    // Get file modification time for versioning
    $script_version = filemtime(plugin_dir_path(__FILE__) . 'cookie-control.js');
    $style_version = filemtime(plugin_dir_path(__FILE__) . 'cookie-control.css');
    
    // Enqueue style with versioning to avoid caching
    wp_enqueue_style(
        'cookie_control_style',
        plugin_dir_url(__FILE__) . 'cookie-control.css',
        array(),
        $style_version
    );

    // Enqueue script with versioning to avoid caching
    wp_enqueue_script(
        'cookie_control_scripts',
        plugin_dir_url(__FILE__) . 'cookie-control.js',
        array(),
        $script_version,
        true // Load in footer
    );
}

function cookie_control($cookieType) {

  switch ($cookieType) :
    case 'tracking':
      if( isset( $_COOKIE['cookieControlTracking']) && $_COOKIE['cookieControlTracking'] == 'accept' ):
        return true;
      endif;
      return false;
      break;
    case 'marketing':
      if( isset( $_COOKIE['cookieControlMarketing']) && $_COOKIE['cookieControlMarketing'] == 'accept' ):
        return true;
      endif;
      return false;
      break;
    case 'advertising':
      if( isset( $_COOKIE['cookieControlAdvertising']) && $_COOKIE['cookieControlAdvertising'] == 'accept' ):
        return true;
      endif;
      return false;
      break;
    case 'essential':
      if( isset( $_COOKIE['cookieControlEssential']) && $_COOKIE['cookieControlEssential'] == 'accept' ):
        return true;
      endif;
      return false;
      break;
    endswitch;
  return false;

}

//Short codes


function tracking_cookies() {
  $output = '<ul class="cookie-control-settings">';

  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="accept" id="tracking-cookies-accept" name="tracking-cookies" class="cookie-control-settings__input"';
  
  if(isset($_COOKIE['cookieControlTracking']) && $_COOKIE['cookieControlTracking'] == 'accept'):
    $output .= ' checked="checked"';
  endif;

  $output .= '><label for="tracking-cookies-accept" class="cookie-control-settings__label">Use cookies that measure my website use</label></li>';

  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="reject" id="tracking-cookies-reject" name="tracking-cookies" class="cookie-control-settings__input"';

  if(isset($_COOKIE['cookieControlTracking']) && $_COOKIE['cookieControlTracking'] == 'reject' || !isset($_COOKIE['cookieControlTracking']) ):
    $output .= ' checked="checked"';
  endif;

  $output .='><label for="tracking-cookies-reject" class="cookie-control-settings__label">Do not use cookies that measure my website use</label></li>';
  $output .= '</ul>';
  return $output;

}

add_shortcode('tracking_cookies', 'tracking_cookies');

function marketing_cookies() {
  $output = '<ul class="cookie-control-settings">';

  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="accept" id="marketing-cookies-accept" name="marketing-cookies" class="cookie-control-settings__input"';
  
  if(isset($_COOKIE['cookieControlMarketing']) && $_COOKIE['cookieControlMarketing'] == 'accept'):
    $output .= ' checked="checked"';
  endif;

  $output .= '><label for="marketing-cookies-accept" class="cookie-control-settings__label">Use cookies that help with communications and marketing</label></li>';

  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="reject" id="marketing-cookies-reject" name="marketing-cookies" class="cookie-control-settings__input"';

  if(isset($_COOKIE['cookieControlMarketing']) && $_COOKIE['cookieControlMarketing'] == 'reject' || !isset($_COOKIE['cookieControlMarketing']) ):
    $output .= ' checked="checked"';
  endif;

  $output .='><label for="marketing-cookies-reject" class="cookie-control-settings__label">Do not use cookies that help with communications and marketing</label></li>';
  $output .= '</ul>';
  return $output;

}

add_shortcode('advertising_cookies', 'advertising_cookies');

function advertising_cookies() {
  $output = '<ul class="cookie-control-settings">';

  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="accept" id="advertising-cookies-accept" name="advertising-cookies" class="cookie-control-settings__input"';
  
  if(isset($_COOKIE['cookieControlAdvertising']) && $_COOKIE['cookieControlAdvertising'] == 'accept'):
    $output .= ' checked="checked"';
  endif;

  $output .= '><label for="advertising-cookies-accept" class="cookie-control-settings__label">Use cookies that help with tailoring advertising</label></li>';

  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="reject" id="advertising-cookies-reject" name="advertising-cookies" class="cookie-control-settings__input"';

  if(isset($_COOKIE['cookieControlAdvertising']) && $_COOKIE['cookieControlAdvertising'] == 'reject' || !isset($_COOKIE['cookieControlAdvertising']) ):
    $output .= ' checked="checked"';
  endif;

  $output .='><label for="advertising-cookies-reject" class="cookie-control-settings__label">Do not use cookies that help with tailoring advertising</label></li>';
  $output .= '</ul>';
  return $output;

}

add_shortcode('marketing_cookies', 'marketing_cookies');

function essential_cookies() {
  $output = '<ul class="cookie-control-settings">';
  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="accept" id="essential-cookies-accept" name="essential-cookies" class="cookie-control-settings__input"';

  if(isset($_COOKIE['cookieControlEssential']) && $_COOKIE['cookieControlEssential'] == 'accept'):
    $output .= ' checked="checked"';
  endif;
  
  $output .='><label for="essential-cookies-accept" class="cookie-control-settings__label">Use cookies that remember my settings on the site</label></li>';
  $output .= '<li class="cookie-control-settings__item"><input type="radio" value="reject" id="essential-cookies-reject" name="essential-cookies" class="cookie-control-settings__input"';

  if(isset($_COOKIE['cookieControlEssential']) && $_COOKIE['cookieControlEssential'] == 'reject' || !isset($_COOKIE['cookieControlEssential']) ):
    $output .= ' checked="checked"';
  endif;

  $output .='><label for="essential-cookies-reject" class="cookie-control-settings__label">Do not use cookies that remember my settings on the site</label></li>';
  $output .= '</ul>';
  return $output;

}

add_shortcode('essential_cookies', 'essential_cookies');


function accept_additional_button($class = null) {
  $output = '<button class="cookie-control-notice__button cookie-control-notice__button--accept js-cookie-control-accept">Accept additional cookies</button>';
  return $output;
}

add_shortcode('accept_additional_button', 'accept_additional_button');

function save_preferences_button($class = null) {
  $output = '<button class="cookie-control-save-button cookie-control-notice__button cookie-control-notice__button--save '.implode($class).'">Save preferences</button>';
  return $output;
}

add_shortcode('save_preferences_button', 'save_preferences_button');

function clear_cookies_button($class = null) {
  $output = '<button class="cookie-control-clear-all-button cookie-control-notice__button cookie-control-notice__button--clear '.implode($class).'">Clear all cookies</button>';
  return $output;
}

add_shortcode('clear_cookies_button', 'clear_cookies_button');

function cookie_notice_page_content() {

  $options = get_option( 'cookie_control_settings_settings' );
  return wpautop(apply_filters('the_content', $options['cookie_control_content']));

}

add_shortcode('cookie_notice_page_content', 'cookie_notice_page_content');


// API endpoints

require_once('cookie-control-notice.php');
require_once('clear-cookies.php');


