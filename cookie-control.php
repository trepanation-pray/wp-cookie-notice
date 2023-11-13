<?php
/*
  Plugin Name: Cookie Control
  Description: EU compliant cookie control
  Version: 2.1.6
  Author: Steven Hill
  Author URI: http://www.stevenhill.me
  License: GPL2
*/


function replace_page_content($content) {
  // Check if it's the page with the specified ID (e.g., 123).
  $options = get_option( 'cookie_control_settings_settings');

  if ( is_page($options['cookie_control_page']) ) {
      $cookie_control_text = $options['cookie_control_content'];
      $content = '<div class="cookie-control-notice__content">' . wp_kses_post($cookie_control_text) . '</div>';
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


function cookie_control_settings_settings_init(  ) {

  register_setting( 'pluginPage', 'cookie_control_settings_settings' );

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
    echo '<p>To use the content above in page use this shortcode: <strong>[cookie_notice_page_content]</strong></p>';

}

function cookie_control_settings_settings_section_callback(  ) {

  echo __( '<p>The EU law now imposes that a communications provider must get consent from the user when storing or accessing information. This includes the use of cookies.</p><p>If no values are set predefined defaults will be used.</p><p>Code and template references can be viewed <a href="https://github.com/trepanation-pray/wp-cookie-notice#readme" target="_blank">here</a></p>', 'Cookie Control Settings' );

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
  wp_enqueue_style( 'cookie_control_style', plugin_dir_url( __FILE__ ) . 'cookie-control.css' );
  wp_enqueue_script( 'cookie_control_scripts', plugin_dir_url( __FILE__ ) . 'cookie-control.js', array(), false, true );
}

function cookie_control($cookieType) {

  switch ($cookieType) :
    case 'tracking':
      if( isset( $_COOKIE['cookieControlTracking']) && $_COOKIE['cookieControlTracking'] == 'accept' ):
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
  $output .= '<button class="cookie-control-save-button cookie-control-notice__button cookie-control-notice__button--save'.implode($class).'">Save preferences</button>';
  return $output;
}

add_shortcode('save_preferences_button', 'save_preferences_button');

function clear_cookies_button($class = null) {
  $output = '<button class="cookie-control-clear-all-button cookie-control-notice__button cookie-control-notice__button--clear'.implode($class).'">Clear all cookies</button>';
  return $output;
}

add_shortcode('clear_cookies_button', 'clear_cookies_button');

function cookie_notice_page_content() {

  $options = get_option( 'cookie_control_settings_settings' );
  return wpautop(apply_filters('the_content', $options['cookie_control_content']));;

}

add_shortcode('cookie_notice_page_content', 'cookie_notice_page_content');


// API endpoints

require_once('cookie-control-notice.php');
require_once('clear-cookies.php');


