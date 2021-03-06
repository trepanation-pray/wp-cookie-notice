<?php
/*
  Plugin Name: Cookie Control
  Description: EU compliant cookie control
  Version: 2.0.3
  Author: Steven Hill
  Author URI: http://www.stevenhill.me
  License: GPL2
*/


// Create cookie control page
function create_cookie_control_page() {

  // Check if cookies page exists
  if( !get_page_by_title('Cookies', 'OBJECT', 'page') ):

    $page = [
      'post_type'     => 'page',
      'post_title'    => 'Cookies',
      'post_content'  => '',
      'post_status'   => 'publish',
      'post_author'   => 1
    ];

    // Insert the post into the database
    wp_insert_post( $page );

  endif;
} 


// Activate the plugin.

function pluginprefix_activate() { 
    create_cookie_control_page(); 
}
register_activation_hook( __FILE__, 'pluginprefix_activate' );



//Settings Page

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
    'cookie_control_settings_text_field_2',
    __( 'Title', 'Cookie Control Settings' ),
    'cookie_control_settings_text_field_2_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_control_settings_text_field_4',
    __( 'Description text', 'Cookie Control Settings' ),
    'cookie_control_settings_text_field_4_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );
  add_settings_field(
    'cookie_control_settings_text_field_6',
    __( 'Show/Hide reject button', 'Cookie Control Settings' ),
    'cookie_control_settings_text_field_6_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );
  add_settings_field(
    'cookie_control_settings_text_field_3',
    __( 'Visual style', 'Cookie Control Settings' ),
    'cookie_control_settings_text_field_3_render',
    'pluginPage',
    'cookie_control_settings_pluginPage_section'
  );
  
}


function cookie_control_settings_text_field_0_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  ?>
  <input type='text' class="regular-text" name='cookie_control_settings_settings[cookie_control_settings_text_field_0]' value='<?php echo $options['cookie_control_settings_text_field_0']; ?>'>
  <?php

}
function cookie_control_settings_text_field_2_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  ?>
  <input type='text' class="regular-text" name='cookie_control_settings_settings[cookie_control_settings_text_field_2]' value='<?php echo $options['cookie_control_settings_text_field_2']; ?>'>
  <?php

}

function cookie_control_settings_text_field_3_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  ?>
  <select name="cookie_control_settings_settings[cookie_control_settings_text_field_3]" id="cookie_control_settings_text_field_3">
    <?php $selected = (isset( $options['cookie_control_settings_text_field_3'] ) && $options['cookie_control_settings_text_field_3'] === 'overlay') ? 'selected' : '' ; ?>
    <option value="overlay" <?php echo $selected; ?>>Overlay</option>
    <?php $selected = (isset( $options['cookie_control_settings_text_field_3'] ) && $options['cookie_control_settings_text_field_3'] === 'inline') ? 'selected' : '' ; ?>
    <option value="inline" <?php echo $selected; ?>>Inline</option>
    <?php $selected = (isset( $options['cookie_control_settings_text_field_3'] ) && $options['cookie_control_settings_text_field_3'] === 'corner') ? 'selected' : '' ; ?>
    <option value="corner" <?php echo $selected; ?>>Corner</option>
     <?php $selected = (isset( $options['cookie_control_settings_text_field_3'] ) && $options['cookie_control_settings_text_field_3'] === 'none') ? 'selected' : '' ; ?>
    <option value="none" <?php echo $selected; ?>>None</option>
  </select><?php

}

function cookie_control_settings_text_field_6_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );
  ?>
  <select name="cookie_control_settings_settings[cookie_control_settings_text_field_6]" id="cookie_control_settings_text_field_6">
    <?php $selected = (isset( $options['cookie_control_settings_text_field_6'] ) && $options['cookie_control_settings_text_field_6'] === 'show') ? 'selected' : '' ; ?>
    <option value="show" <?php echo $selected; ?>>Show</option>
    <?php $selected = (isset( $options['cookie_control_settings_text_field_6'] ) && $options['cookie_control_settings_text_field_6'] === 'hide') ? 'selected' : '' ; ?>
    <option value="hide" <?php echo $selected; ?>>Hide</option>
  </select><?php

}

function cookie_control_settings_text_field_4_render(  ) {

  $options = get_option( 'cookie_control_settings_settings' );

    if( $options['cookie_control_settings_text_field_4'] ) {
      $cookie_control_text = $options['cookie_control_settings_text_field_4'];
    } else {
      $cookie_control_text = 'We use some essential cookies to make this website work.'.PHP_EOL.PHP_EOL.'We’d like to set additional cookies to understand how you use the website, remember your settings and improve you services.'.PHP_EOL.PHP_EOL.'We also use cookies set by other sites to help us deliver content from their services.';
    }
  ?>
  <textarea class="regular-text" rows="5" name='cookie_control_settings_settings[cookie_control_settings_text_field_4]'><?= $cookie_control_text ?></textarea>
  <?php

}

function cookie_control_settings_settings_section_callback(  ) {

  echo __( '<p>The EU law now imposes that a communications provider must get consent from the user when storing or accessing information. This includes the use of cookies.</p><p>If no values are set predefined defaults will be used.</p><p>Code and template references can be viewed <a href="https://github.com/trepanation-pray/wp-cookie-notice#readme" target="_blank">here</a></p><p>Default Page content wigth controls can be viewed <a href="https://github.com/trepanation-pray/wp-cookie-notice/blob/master/cookies-page-content.html" target="_blank">here</a></p>', 'Cookie Control Settings' );

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


//Front-end html

function cookie_control_notice( ) {

  if(!isset( $_COOKIE['cookieControlTracking']) || !isset($_COOKIE['cookieControlEssential'] )):

    //Retrieve the values
    $options = get_option( 'cookie_control_settings_settings' );
    
    
    //Set desktop title
    if( $options['cookie_control_settings_text_field_2'] ) {
      $cookie_control_title = $options['cookie_control_settings_text_field_2'];
    } else {
      $cookie_control_title = 'Cookie controls';
    }
    //Set desktop text
    $cookie_control_text = $options['cookie_control_settings_text_field_4'];
    
    $cookie_control_text = str_replace("\n\r", "</p>\n<p>", $cookie_control_text);
    $cookie_control_text = "<p>" . $cookie_control_text . "</p>";
    
    //Visual style
    $cookie_control_style = $options['cookie_control_settings_text_field_3'];

    //Reject button
    $cookie_control_reject_button = ($options['cookie_control_settings_text_field_6'] === 'show');

    //Set read more link
    $cookie_control_link = '/cookies';
   

    
    //Construct the HTML
    $output = '<div class="cookie-control-notice cookie-control-notice--'.$cookie_control_style .'">';
      $output .= '<div class="cookie-control-notice__container">';

        $output .= '<h2 class="cookie-control-notice__title">'.$cookie_control_title.'</h2>';
    
        $output .= '<div class="cookie-control-notice__text">'.$cookie_control_text.'</div>';
        
        $output .= '<p><button class="cookie-control-notice__button cookie-control-notice__button--accept js-cookie-control-accept">Accept additional cookies</button>';
        
        if($cookie_control_reject_button):
          $output .= ' <button class="cookie-control-notice__button cookie-control-notice__button--reject js-cookie-control-accept">Reject additional cookies</button></p>';
        endif;

        $output .= '<p><a href="'.get_home_url().$cookie_control_link.'" class="cookie-control-notice__link">View cookie controls and policy</a></p>';

      $output .= '</div>';
    $output .= '</div>';

    //Output the html
    echo $output;

  endif;

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

  if(isset($_COOKIE['cookieControlTracking']) && $_COOKIE['cookieControlTracking'] == 'reject'):
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
    if(isset($_COOKIE['cookieControlEssential']) && $_COOKIE['cookieControlEssential'] == 'reject'):
    $output .= ' checked="checked"';
  endif;
  $output .='><label for="essential-cookies-reject" class="cookie-control-settings__label">Do not use cookies that remember my settings on the site</label></li>';
  $output .= '</ul>';
  return $output;

}

add_shortcode('essential_cookies', 'essential_cookies');


function save_preferences_button($class = null) {
  $output = '<button class="cookie-control-save-button '.implode($class).'">Save preferences</button>';
  return $output;
}

add_shortcode('save_preferences_button', 'save_preferences_button');

function clear_cookies_button($class = null) {
  $output = '<button class="cookie-control-clear-all-button '.implode($class).'">Clear all cookies</button>';
  return $output;
}

add_shortcode('clear_cookies_button', 'clear_cookies_button');