<?php

/* 

Plugin Name: adBuddy+ (AdBlocker Detection)
Description: Display a pop-up notice to ask your visitors to disable their AdBlocker add-on for Firefox. Customize the display image, welcome text, text message and more all from the options page. This plugin is built using Jmlevick's adBuddy + jsBuddy software. Free to use and all credit goes to Jmlevick. View the original project here: https://github.com/Jmlevick/adbuddy-jsbuddy.
Version: 1.1.3
Date: 07/03/14 
Author: NetfunkDesign 
Author URI: http://www.netfunkdesign.com/contact.php
Plugin URI: http://www.netfunkdesign.com/adbuddyplus/ 

 USAGE:

 1. Activate the plugin. 
 2. You will also need to add the class 'adbuddy-protected' to your ads 

 CREDITS: 
 
 adBuddy + jsBuddy webiste: https://github.com/Jmlevick/adbuddy-jsbuddy 
  
*/

defined('ABSPATH') or die("No script kiddies please!");
define('ADBUDDY_SECURED','You do not have sufficient permissions to access this page.');
define('ADBUDDY_IMG', plugin_dir_url(__FILE__).'img/stop-adblock.png');
define('ADBUDDY_TITLE','Please disable your AdBlocker...');
define('ADBUDDY_MSG','The few ads we do display help to keep this site a live. Every cent helps so please disable your AdBlocker for this website.');
define('ADBUDDY_BUTTON','Reload Page');
define('ADBUDDY_JS_TITLE','You have JS disabled...');
define('ADBUDDY_JS_MSG','Notice that you need to enable javascript in order to use our site, Thanks!');

/* addBuddy CSS */
function netfunk_adbuddy_css() {
	wp_register_style( 'adbuddy-css', plugin_dir_url(__FILE__) . 'style.css' );
	wp_enqueue_style( 'adbuddy-css' );
}
add_action('wp_print_styles', 'netfunk_adbuddy_css');


/* addBuddy Options Datastore */
function netfunk_adbuddy_menu() {
	add_options_page( 'adBuddy+ Options', 'adBuddy+', 'manage_options', 'adbuddy', 'adbuddy_options_page' );
}
add_action( 'admin_menu', 'netfunk_adbuddy_menu' );


/* Register addBuddy Settings */
add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){
  register_setting( 'adbuddy_options', 'adbuddy_options', 'adbuddy_options_validate' );
  add_settings_section('adbuddy_main', '', 'plugin_section_text', 'adbuddy');
  add_settings_field('adbuddy_force', 'Require for all visitors', 'adbuddy_force', 'adbuddy', 'adbuddy_main');
  add_settings_field('adbuddy_img_preview',  'Display An Image', 'adbuddy_img_preview', 'adbuddy', 'adbuddy_main');
  add_settings_field('adbuddy_display_img', 'Upload Image', 'adbuddy_display_img', 'adbuddy', 'adbuddy_main');
  add_settings_field('adbuddy_title', 'Custom Title', 'adbuddy_title', 'adbuddy', 'adbuddy_main');
  add_settings_field('adbuddy_message', 'Custom Message', 'adbuddy_message', 'adbuddy', 'adbuddy_main');
  add_settings_field('adbuddy_button', 'Button Label', 'adbuddy_button', 'adbuddy', 'adbuddy_main');
  
  /* populate predfined settings */
  $options = get_option('adbuddy_options');
  add_option( 'adbuddy_options', $options,'','yes');
  
}

/* Plugin section title */
function plugin_section_text() {
  echo '<p>Display a pop-up notice to ask your visitors to disable their AdBlocker add-on. Also detects if scripting is active.</p>';
  echo '<hr />';
  echo '<p><strong>Important:</strong><br /> '
  .'This currently only detects some google ads. If you have ads on your site but the pop-up notice is not appearing; You will need to add the CSS class <i>\' .adbuddy-protected \'</i> to your ad container(s).</p>';
  echo '<hr />';
}

function adbuddy_force() {
  $options = get_option('adbuddy_options');
  echo '<p>Make this mandatory to view your website. <br /><small>(Setting this to \'No\' allows visitors to close the pop-up notice without disabling AdBlocker. The pop-up will persist from page to page)</small><br /><br /> ';
  echo '<label><input id="adbuddy_foce" name="adbuddy_options[force]" type="radio" value="1" ' . checked( 1, $options['force'], false ) . '/>Yes</label> &nbsp;&nbsp; ';
  echo '<label><input id="adbuddy_foce" name="adbuddy_options[force]" type="radio" value="0" ' . checked( 0, $options['force'], false ) . '/>No</label> <br />';
}

function adbuddy_title() {
  $options = get_option('adbuddy_options');
  echo '<input id="adbuddy_title" name="adbuddy_options[title]" size="40" type="text" value="'.( !empty( $options['title'] ) ? $options['title'] : ADBUDDY_TITLE ).'" />';
}

function adbuddy_message() {
  $options = get_option('adbuddy_options');
  echo '<textarea id="adbuddy_message" name="adbuddy_options[message]" cols="40" />'.( !empty( $options['message'] ) ? $options['message'] : ADBUDDY_MSG ).'</textarea>';
}

function adbuddy_button() {
  $options = get_option('adbuddy_options');
  echo '<input id="adbuddy_button" name="adbuddy_options[button]" size="30" type="text" value="'.( !empty( $options['button'] ) ? $options['button'] : ADBUDDY_BUTTON ).'" />';
}

function adbuddy_img_preview() {
    $options = get_option( 'adbuddy_options' );  ?>
    <div id="upload_image_preview">
        <img style="max-width:300px" src="<?php echo esc_url( ( !empty( $options['display_img'] ) ? $options['display_img'] : ADBUDDY_BUTTON ) ); ?>" />
    </div>
    <?php
}

/* hack-in image uploader bits */
function adbuddy_display_img() {
    $options = get_option( 'adbuddy_options' );
    ?>
        <input type="hidden" id="adbuddy_display_img" name="adbuddy_options[display_img]" value="<?php echo esc_url( ( !empty( $options['display_img'] ) ? $options['display_img'] : ADBUDDY_BUTTON ) ); ?>" />
        <input id="upload_img_button" type="button" class="button" value="<?php _e( 'Upload image', 'adbuddy' ); ?>" />
        <?php if ( $options['display_img'] != ADBUDDY_IMG ): ?>
            <input id="delete_logo_button" name="adbuddy_options[delete_logo]" type="submit" class="button" value="<?php _e( 'Delete Image', 'adbuddy' ); ?>" />
        <?php endif; ?>
        <span class="description"><?php _e('Upload an image for the display image.', 'adbuddy' ); ?></span>
    <?php
}

/* enqueue upload js */
function adbuddy_enqueue_scripts() {
    wp_register_script( 'adbuddy-upload', plugin_dir_url(__FILE__).'upload.js', array('jquery','media-upload','thickbox') );
 
    if ( 'settings_page_adbuddy' == get_current_screen() -> id ) {
        wp_enqueue_script('jquery');
 
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
 
        wp_enqueue_script('media-upload');
        wp_enqueue_script('adbuddy-upload');
 
    }
 
}
add_action('admin_enqueue_scripts', 'adbuddy_enqueue_scripts');

/* hack-in image select button title */
function adbuddy_options_setup() {
    global $pagenow;
 
    if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
        // Now we'll replace the 'Insert into Post Button' inside Thickbox
        add_filter( 'gettext', 'replace_thickbox_text'  , 1, 3 );
    }
}
add_action( 'admin_init', 'adbuddy_options_setup' );

function replace_thickbox_text($translated_text, $text, $domain) {
    if ('Insert into Post' == $text) {
        $referer = strpos( wp_get_referer(), 'adbuddy' );
        if ( $referer != '' ) {
            return __('Use this image', 'adbuddy' );
        }
    }
    return $translated_text;
}

/* remove custom display image ( reverts to default ) */
function adbuddy_delete_image( $image_url ) {
    global $wpdb;
 
    // We need to get the image's meta ID.
    $query = "SELECT ID FROM wp_posts where guid = '" . esc_url($image_url) . "' AND post_type = 'attachment'";
    $results = $wpdb->get_results($query);
 
    // And delete it
    foreach ( $results as $row ) {
        wp_delete_attachment( $row->ID );
    }
}

/* addBuddy Options Validate */
function adbuddy_options_validate($input) {

  $options['title'] = (!empty($input['title']) ? trim($input['title']) : ADBUDDY_TITLE );
  $options['message'] = (!empty($input['message']) ? trim($input['message']) : ADBUDDY_MSG );
  $options['button'] = (!empty($input['button']) ? trim($input['button']) : ADBUDDY_BUTTON );
  $options['display_img'] = (!empty($input['display_img']) ? trim($input['display_img']) : ADBUDDY_IMG);
  $options['force'] = (isset($input['force']) ? $input['force'] : 1);
  
  $delete_logo = ! empty($input['delete_logo']) ? true : false;
  if ( $delete_logo ) {
    adbuddy_delete_image( $input['display_img'] );
    $options['display_img'] = ADBUDDY_IMG;
 }
  
  return $options;

}

/* addBuddy Options Page */
function adbuddy_options_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( ADBUDDY_SECURED ) );
	}
	
	echo '<div class="wrap">';
	echo '<h2>adBuddy+ (AdBlocker Detection)</h2>';
	echo '<form method="post" action="options.php">';
	
	settings_fields( 'adbuddy_options' );
	do_settings_sections( 'adbuddy' );
	
    echo '<hr />';
	submit_button(); 
    echo '</form>';
    echo '</div>';
	
	/* Debug  */
	  //$options = get_option( 'adbuddy_options' );
	  //echo '<pre>';
	  //echo '<h6>debug</h6>';
	  //print_r ($options);
	  //echo '</pre>';
	
}

/* adBuddy JS added to Wordpress page footer */
if (!function_exists( 'netfunk_adbuddy')){
  function netfunk_adbuddy_script() {
	if ( !is_admin() ) {

	  $options = get_option( 'adbuddy_options' );
      $jQeury = "jQuery"; // the jquery variable ( incase you are using older Foundation or another framework. The default setting would be '$' ) 
	  $title = (isset($options['title']) ? $options['title'] : ADBUDDY_TITLE);
	  $message = (isset($options['message']) ? $options['message'] : ADBUDDY_MSG);
      $button = (isset($options['button']) ? $options['button'] : ADBUDDY_BUTTON);

	  echo "<script>";
	  
	  echo "var closeAdbuddy; closeAdbuddy=function(){
			jQuery('#adbuddy-no-adb-container').hide();
			jQuery('#adbuddy-overlay').hide();
	  };";
	  
	  echo 'jQuery(document).ready(function($) {';
	  echo "$(\".adsbygoogle\").addClass(\"adbuddy-protected\"); ";
	  echo "var adBuddy;
	adBuddy=function(){var a;a=function(){var a,b;b=\"\";for(a=0;8>a;)b+=\"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\".charAt(Math.floor(62*Math.random())),a++;return window.adBuddycaptcha=b};$(\".adbuddy-protected\").each(function(){if(1024>screen.width){if(!1===$(this).is(\":visible\")&&-1===$($(this))[0].className.indexOf(\"not-mobile\"))return a(),window.adBuddytoken=[\"mobile\",adBuddycaptcha]}else if(!1===$(this).is(\":visible\"))return a(),window.adBuddytoken=[\"desktop\",adBuddycaptcha]});if(\"undefined\"!==
	typeof adBuddytoken&&null!==adBuddytoken&&\"mobile\"===adBuddytoken[0])return $(\"body\").append(\"<div id='adbuddy-overlay'></div><div id='adbuddy-no-adb-container'>".($options['force'] != 1 ? "<a href='#' onclick='closeAdbuddy();return'><img src='".plugin_dir_url(__FILE__)."img/close.png' id='adbuddy-close-button'></a>" : '')."<p class='adbuddy-p'><img id='adbuddy-stopadblock' src='".plugin_dir_url(__FILE__)."img/stop-adblock.png' alt='stop-adblock'></p><h3>".$title."</h3><p class='adbuddy-p' style='margin-bottom: 15px;'>".$message."</p><div id='adbuddy-no-adb-suggestions'><a href='#' id='adbuddy-donebutton' onclick='location.reload();' class='button success radius'>".$button."</a></div></div>\"),
	$(\"#adbuddy-overlay\").show(),$(\"#adbuddy-no-adb-container\").show();if(\"undefined\"!==typeof adBuddytoken&&null!==adBuddytoken&&\"desktop\"===adBuddytoken[0])return $(\"body\").append(\"<div id='adbuddy-overlay'></div><div id='adbuddy-no-adb-container'>".($options['force'] != 1 ? "<a href='#' onclick='closeAdbuddy();return'><img src='".plugin_dir_url(__FILE__)."img/close.png' id='adbuddy-close-button'></a>" : '')."<p class='adbuddy-p'><img id='adbuddy-stopadblock' src='".plugin_dir_url(__FILE__)."img/stop-adblock.png' alt='stop-adblock'></p><h3>".$title."</h3><p class='adbuddy-p' style='margin-bottom: 15px;'>".$message."</p><div id='adbuddy-no-adb-suggestions'><a href='#' id='adbuddy-donebutton' onclick='location.reload();' class='button success radius'>".$button."</a></div></div>\"),
	$(\"#adbuddy-overlay\").show(),$(\"#adbuddy-no-adb-container\").show()};";
	  echo "$(document).ready(function(){adBuddy();return $(\".adsbygoogle\").addClass(\"adbuddy-protected\");});});";
	  echo '</script>';
	}
  }
}
add_action( 'wp_footer', 'netfunk_adbuddy_script');

/* adBuddy NoScript added to page footer */
if (!function_exists( 'netfunk_adbuddy')){
  function netfunk_adbuddy() {
    if ( !is_admin() ) {
    echo "<noscript id=\"js-warning\">
<div id='jsbuddy-overlay'></div><div id='jsbuddy-no-adb-container'><p class='jsbuddy-p'><img id='jsbuddy-stopadblock' src='".plugin_dir_url(__FILE__)."img/jslogo.png' alt='stop-jsblock'></p><p class='jsbuddy-p'><strong>".ADBUDDY_JS_TITLE."</strong></p><p class='jsbuddy-p' style='margin-bottom: 15px;'>".ADBUDDY_JS_MSG."</p><div id='jsbuddy-no-adb-suggestions'></div></div>
</noscript>";
    }
  }
}

add_action( 'wp_footer', 'netfunk_adbuddy');

// EOF