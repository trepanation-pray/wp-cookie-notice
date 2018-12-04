<?php
/*
  Plugin Name: Simple Cookie Notice
  Description: EU compliant cookie notice
  Version: 1.5
  Author: Steven Hill
  Author URI: http://www.stevenhill.me
  License: GPL2
*/


//Settings Page

add_action( 'admin_menu', 'cookie_notice_settings_add_admin_menu' );
add_action( 'admin_init', 'cookie_notice_settings_settings_init' );


function cookie_notice_settings_add_admin_menu(  ) {

  add_options_page( 'Cookie Notice', 'Cookie Notice', 'manage_options', 'cookie_notice', 'cookie_notice_settings_options_page' );

}


function cookie_notice_settings_settings_init(  ) {

  register_setting( 'pluginPage', 'cookie_notice_settings_settings' );

  add_settings_section(
    'cookie_notice_settings_pluginPage_section',
    __( '<h1>Cookie Notice</h1>', 'Cookie Notice Settings' ),
    'cookie_notice_settings_settings_section_callback',
    'pluginPage'
  );

  add_settings_field(
    'cookie_notice_settings_text_field_0',
    __( 'Cookie notice mobile text', 'Cookie Notice Settings' ),
    'cookie_notice_settings_text_field_0_render',
    'pluginPage',
    'cookie_notice_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_notice_settings_text_field_4',
    __( 'Cookie notice full text', 'Cookie Notice Settings' ),
    'cookie_notice_settings_text_field_4_render',
    'pluginPage',
    'cookie_notice_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_notice_settings_text_field_1',
    __( 'Find out more link', 'Cookie Notice Settings' ),
    'cookie_notice_settings_text_field_1_render',
    'pluginPage',
    'cookie_notice_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_notice_settings_text_field_2',
    __( 'Foreground colour', 'Cookie Notice Settings' ),
    'cookie_notice_settings_text_field_2_render',
    'pluginPage',
    'cookie_notice_settings_pluginPage_section'
  );

  add_settings_field(
    'cookie_notice_settings_text_field_3',
    __( 'Background colour', 'Cookie Notice Settings' ),
    'cookie_notice_settings_text_field_3_render',
    'pluginPage',
    'cookie_notice_settings_pluginPage_section'
  );

}


function cookie_notice_settings_text_field_0_render(  ) {

  $options = get_option( 'cookie_notice_settings_settings' );
  ?>
  <input type='text' class="regular-text" name='cookie_notice_settings_settings[cookie_notice_settings_text_field_0]' value='<?php echo $options['cookie_notice_settings_text_field_0']; ?>'>
  <?php

}

function cookie_notice_settings_text_field_1_render(  ) {

  $options = get_option( 'cookie_notice_settings_settings' );
  ?>
  <input type='text' class="regular-text" name='cookie_notice_settings_settings[cookie_notice_settings_text_field_1]' value='<?php echo $options['cookie_notice_settings_text_field_1']; ?>'>
  <?php

}

function cookie_notice_settings_text_field_4_render(  ) {

  $options = get_option( 'cookie_notice_settings_settings' );
  ?>
  <textarea class="regular-text" rows="5" name='cookie_notice_settings_settings[cookie_notice_settings_text_field_4]'><?php echo $options['cookie_notice_settings_text_field_4']; ?></textarea>
  <?php

}
function cookie_notice_settings_text_field_2_render(  ) {

  $options = get_option( 'cookie_notice_settings_settings' );
  ?>
  <input type='text' class="regular-text" name='cookie_notice_settings_settings[cookie_notice_settings_text_field_2]' value='<?php echo $options['cookie_notice_settings_text_field_2']; ?>'>
  <?php

}

function cookie_notice_settings_text_field_3_render(  ) {

  $options = get_option( 'cookie_notice_settings_settings' );
  ?>
  <input type='text' class="regular-text" name='cookie_notice_settings_settings[cookie_notice_settings_text_field_3]' value='<?php echo $options['cookie_notice_settings_text_field_3']; ?>'>
  <?php

}
function cookie_notice_settings_settings_section_callback(  ) {

  echo __( '<p>The EU law now imposes that a communications provider must get consent from the user when storing or accessing information. This includes the use of cookies.</p><p>If no values are set predefined defaults will be used.</p>', 'Cookie Notice Settings' );

}


function cookie_notice_settings_options_page(  ) {

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



//Front-end html

add_action('wp_footer', 'cookie_notice_html');

function cookie_notice_html(  ) {

  if(!isset($_COOKIE['cookie-notice'])):

    //Retrieve the values
    $options = get_option( 'cookie_notice_settings_settings' );

    //Set desktop text
    if( $options['cookie_notice_settings_text_field_4'] ) {
      $cookie_notice_full_text = $options['cookie_notice_settings_text_field_4'];
    } else {
      $cookie_notice_full_text = 'This website uses cookies for essential functionality and to improve your user experience. By using this website you are giving your consent for us to set cookies.';
    }

    //Set mobile text
    if( $options['cookie_notice_settings_text_field_0'] ) {
      $cookie_notice_mobile_text = $options['cookie_notice_settings_text_field_0'];
    } else {
      $cookie_notice_mobile_text = 'This website uses cookies.';
    }

    //Set read more link
    if( $options['cookie_notice_settings_text_field_1'] ) {
      $cookie_notice_link = $options['cookie_notice_settings_text_field_1'];
    } else {
      $cookie_notice_link = get_privacy_policy_url();
    }
    
    //Set text colour
    if( $options['cookie_notice_settings_text_field_2'] ) {
      $cookie_notice_text_colour = $options['cookie_notice_settings_text_field_2'];
    } else {
      $cookie_notice_text_colour = 'white';
    }

    //Set background colour
    if( $options['cookie_notice_settings_text_field_3'] ) {
      $cookie_notice_background_colour = $options['cookie_notice_settings_text_field_3'];
    } else {
      $cookie_notice_background_colour = '#3C3C3C';
    }

    //Close button svg
    function cookie_notice_close_svg( $path_colour ) {
      return rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="'.$path_colour.'" d="M23.954 21.03l-9.184-9.095 9.092-9.174-2.832-2.807-9.09 9.179-9.176-9.088-2.81 2.81 9.186 9.105-9.095 9.184 2.81 2.81 9.112-9.192 9.18 9.1z"/></svg>');
    }
    
    //Construct the HTML
    $output = '<div id="js-cookie-notice" class="o-cookie-notice">';
    $output .= '<span class="o-cookie-notice__mobile-text">'.$cookie_notice_mobile_text.'</span>';
    $output .= '<span class="o-cookie-notice__full-text">'.$cookie_notice_full_text.'</span>';
    if ( $cookie_notice_link ) {
      $output .= ' <a href="'.$cookie_notice_link.'" class="o-cookie-notice__link" onclick="cookieFunc();">Find out more</a>';
    }
    $output .= '<a class="o-cookie-notice__close" title="Close this notice" onclick="cookieFunc();">Close notice</a>';
    $output .= '</div>';

    //Styling
    $output .= '<style>.o-cookie-notice {position: fixed; z-index: 999999; font-size: 14px; font-family: sans-serif; line-height: 1.5; bottom: 0; left: 0; width: 100%; padding: 10px 45px 10px 10px; color: '.$cookie_notice_text_colour.'; background: '.$cookie_notice_background_colour.';}.o-cookie-notice__link {text-decoration: underline; color: '.$cookie_notice_text_colour.'; transition: .3s;}.o-cookie-notice__link:hover {opacity: 0.5; text-decoration: none; color: '.$cookie_notice_text_colour.';} .o-cookie-notice__close {position: absolute; transform: translatey(-50%); top: 50%; right: 6px; height: 30px; width: 30px; text-indent: -9999px; border: 2px solid '.$cookie_notice_text_colour.'; background: url(\'data:image/svg+xml;charset=utf8,'.cookie_notice_close_svg($cookie_notice_text_colour).'\') center no-repeat; background-size: 13px 13px; border-radius: 50%; overflow: hidden; transition: .3s;} .o-cookie-notice__close:hover {background-image: url(\'data:image/svg+xml;charset=utf8,'.cookie_notice_close_svg($cookie_notice_background_colour).'\'); background-color: '.$cookie_notice_text_colour.'; transform: translatey(-50%) scale(.8) }.o-cookie-notice__full-text {display: none;}@media (min-width: 680px) {.o-cookie-notice__mobile-text {display: none;}.o-cookie-notice__full-text {display: inline;}}</style>';

    //Javascript - set cookie and body offset
    $output .= '<script>document.body.style.marginBottom = document.getElementById("js-cookie-notice").clientHeight + "px"; function cookieFunc(){document.cookie = "cookie-notice=acknowledged cookie use; expires=Thu, 1 Jan 2099 12:00:00 GMT; path=/";document.getElementById("js-cookie-notice").remove();document.body.style.marginBottom = "0";}</script>';

    //Output the html
    echo $output;

  endif;

}
