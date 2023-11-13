<?php
function cookie_notice_content() {
  
  //Retrieve the values
  $options = get_option( 'cookie_control_settings_settings' );
  
  
  //Set desktop title
  if( $options['cookie_control_title'] ) {
    $cookie_control_title = $options['cookie_control_title'];
  } else {
    $cookie_control_title = 'Cookie controls';
  }
  //Set desktop text
  $cookie_control_text = $options['cookie_control_description'];
  
  $cookie_control_text = str_replace("\n\r", "</p>\n<p>", $cookie_control_text);
  $cookie_control_text = "<p>" . $cookie_control_text . "</p>";

  $cookie_control_content = wpautop(apply_filters('the_content', $options['cookie_control_content']));
  
  //Set read more link
  $cookie_control_link = get_permalink($options['cookie_control_page']);

  //Visual style
  $cookie_control_style = $options['cookie_control_style'];

  //Reject button
  $cookie_control_reject_button = ($options['cookie_control_reject_button'] === 'show');


  
  //Construct the HTML
  $output = '<div class="cookie-control-notice cookie-control-notice--'.$cookie_control_style .'">';
    $output .= '<div class="cookie-control-notice__container">';
    
      $output .= '<h2 class="cookie-control-notice__title">'.$cookie_control_title.'</h2>';

      $output .= '<div class="cookie-control-notice__landing">';

    
        $output .= '<div class="cookie-control-notice__text">'.$cookie_control_text.'</div>';
        
        $output .= '<p>';

          $output .= '<button class="cookie-control-notice__button cookie-control-notice__button--accept js-cookie-control-accept">Accept additional cookies</button>';
        
          if($cookie_control_reject_button):
            $output .= '<button class="cookie-control-notice__button cookie-control-notice__button--reject js-cookie-control-accept">Reject additional cookies</button>';
          endif;

          if($cookie_control_style == 'overlay'):
            $output .= '<button class="cookie-control-notice__button cookie-control-notice__button--manage js-cookie-control-manage">Manage Preferences</button>';
          endif;
        
        $output .= '</p>';


        // $output .= '<p><a href="'.$cookie_control_link.'" class="cookie-control-notice__link">View cookie controls and policy</a></p>';

      $output .= '</div>';
      
      $output .= '<div class="cookie-control-notice__manage">';

        $output .= '<div class="cookie-control-notice__content">';
          
        $output .=  $cookie_control_content;

        
        $output .= '</div>';
      $output .= '</div>';

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
