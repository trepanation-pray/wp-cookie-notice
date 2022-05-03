<?php
function cookie_notice_content() {
  
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
  
  //Set read more link
  $cookie_control_link = get_permalink($options['cookie_control_settings_text_field_5']);

  //Visual style
  $cookie_control_style = $options['cookie_control_settings_text_field_3'];

  //Reject button
  $cookie_control_reject_button = ($options['cookie_control_settings_text_field_6'] === 'show');


  
  //Construct the HTML
  $output = '<div class="cookie-control-notice cookie-control-notice--'.$cookie_control_style .'">';
    $output .= '<div class="cookie-control-notice__container">';

      $output .= '<h2 class="cookie-control-notice__title">'.$cookie_control_title.'</h2>';
  
      $output .= '<div class="cookie-control-notice__text">'.$cookie_control_text.'</div>';
      
      $output .= '<p><button class="cookie-control-notice__button cookie-control-notice__button--accept js-cookie-control-accept">Accept additional cookies</button>';
      
      if($cookie_control_reject_button):
        $output .= ' <button class="cookie-control-notice__button cookie-control-notice__button--reject js-cookie-control-accept">Reject additional cookies</button></p>';
      endif;

      $output .= '<p><a href="'.$cookie_control_link.'" class="cookie-control-notice__link">View cookie controls and policy</a></p>';

    $output .= '</div>';
  $output .= '</div>';

  //Output the html
  echo $output;

}

add_action( 'rest_api_init', function() {
  register_rest_route( 'cookie-control', '/notice-content', array(          
    'methods'  => 'GET',
    'callback' => 'cookie_notice_content',
    'permission_callback' => '__return_true'
  ) ); 
} );
