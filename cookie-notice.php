<?php
/*
   Plugin Name: Simple Cookie Notice
   Description: EU compliant cookie notice
   Version: 18.03.21
   Author: Steven Hill
   Author URI: http://www.stevenhill.io
   License: GPL2
*/


//Settings Page

add_action( 'admin_menu', 'cookie_notice_settings_add_admin_menu' );
add_action( 'admin_init', 'cookie_notice_settings_settings_init' );


function cookie_notice_settings_add_admin_menu(  ) {

    add_options_page( 'Cookie Notice', 'Cookie Notice', 'manage_options', 'pentascape_cookie_notice', 'cookie_notice_settings_options_page' );

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

    echo __( '<p>The EU law now imposes that a communications provider must get consent from the user when storing or accessing information. This includes the use of cookies.</p>', 'Cookie Notice Settings' );

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
        $cookie_notice_mobile_text = $options['cookie_notice_settings_text_field_0'];
        $cookie_notice_full_text = $options['cookie_notice_settings_text_field_4'];

        $cookie_notice_link = $options['cookie_notice_settings_text_field_1'];
        $cookie_notice_text_colour = $options['cookie_notice_settings_text_field_2'];
        $cookie_notice_background_colour = $options['cookie_notice_settings_text_field_3'];

        //Construct the HTML
        $output = '<div id="cookie-notice" class="cookie-notice-container">';
        $output .= '<span class="mobile-text">'.$cookie_notice_mobile_text.'</span>';
        $output .= '<span class="full-text">'.$cookie_notice_full_text.'</span>';
        $output .= ' <a href="'.$cookie_notice_link.'" class="cookie-notice-link close-cookie-notice" onclick="cookieFunc();">Find out more</a>';
        $output .= '<a class="cookie-notice-close close-cookie-notice" title="Close this notice" onclick="cookieFunc();">Close notice</a>';
        $output .= '</div>';

        //Styling
        $output .= '<style>.cookie-notice-container {position: fixed; z-index: 999999; font-size: 14px; font-family: sans-serif; line-height: 1.5; bottom: 0; left: 0; width: 100%; padding: 10px 45px 10px 10px; box-shadow: 0 0 15px rgba(0,0,0,.25), 0 0 5px rgba(0,0,0,.5); color: white; background: '.$cookie_notice_background_colour.';}.cookie-notice-link {text-decoration: underline; color: '.$cookie_notice_text_colour.'; transition: .3s;}.cookie-notice-link:hover {opacity: 0.5; color: white;} .cookie-notice-close {position: absolute; transform: translatey(-50%); top: 50%; right: 6px; height: 30px; width: 30px; text-indent: -9999px; border: 2px solid '.$cookie_notice_text_colour.'; background: url(\'data:image/svg+xml;charset=utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="'.$cookie_notice_text_colour.'" d="M23.954 21.03l-9.184-9.095 9.092-9.174-2.832-2.807-9.09 9.179-9.176-9.088-2.81 2.81 9.186 9.105-9.095 9.184 2.81 2.81 9.112-9.192 9.18 9.1z"/></svg>\') center no-repeat; background-size: 13px 13px; border-radius: 50%; overflow: hidden; transition: .3s;} .cookie-notice-close:hover {opacity: 0.5;}.full-text {display: none;}@media (min-width: 680px) {.mobile-text {display: none;}.full-text {display: inline;}}</style>';

        //Javascript - set cookie
        $output .= '<script>function cookieFunc(){document.cookie = "cookie-notice=acknowledged cookie use; expires=Thu, 1 Jan 2099 12:00:00 GMT; path=/";document.getElementById("cookie-notice").remove()}</script>';

        //Output the html
        echo $output;

    endif;

}
