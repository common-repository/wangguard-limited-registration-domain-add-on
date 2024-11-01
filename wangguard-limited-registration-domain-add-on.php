<?php
/*
Plugin Name: WangGuard Limited Registration Domain Add-On
Plugin URI: http://www.wangguard.com
Description: Limit registration to certains domains or block certains domains . WangGuard plugin version 1.6 or higher is required, download it for free from <a href="http://wordpress.org/extend/plugins/wangguard/">http://wordpress.org/extend/plugins/wangguard/</a>.
Version: 1.0.0
Author: WangGuard
Author URI: http://www.wangguard.com
License: GPL2
*/

/*  Copyright 2012  WangGuard (email : info@wangguard.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WANGGUARD_LIMITE_REGISTRATION_DOMAIN', '1.0');

function wangguard_limit_domain_registration_init() {

if (function_exists('load_plugin_textdomain')) {
		$plugin_dir = basename(dirname(__FILE__));
		load_plugin_textdomain('wangguard-limit-domain-registration-add-on', false, $plugin_dir . "/languages/" );
	}
}
add_action('init', 'wangguard_limit_domain_registration_init');

function wangguard_limit_domain_regisration_activate() {

	add_site_option('wangguard_limited_email_domains','');
	add_site_option('wangguard_banned_email_domains','');

}
register_activation_hook( 'wangguard-limited-registration-domain-add-on/wangguard-limited-registration-domain-add-on.php', 'wangguard_limit_domain_regisration_activate' );


function wangguard_limit_domain_regisration_notices() {
	if ( !defined('WANGGUARD_VERSION') ) {
		echo "
		<div  class='error fade'><p><strong>".__('WangGuard Limited Registration Domain Add-On is almost ready.', 'wangguard-registration-add-on')."</strong> ". __('You must install and activate <a href="http://wordpress.org/extend/plugins/wangguard/">WangGuard</a> 1.6-RC1 or higher to use this plugin.', 'wangguard-registration-add-on')."</p></div>
		";
	}
	else {
		if ( defined('WANGGUARD_VERSION') ) {$version = WANGGUARD_VERSION;}
		if ($version)
		if (version_compare($version , '1.6-RC1') == -1)
			echo "
			<div  class='error fade'><p><strong>".__('WangGuard Limited Registration Domain Add-On is almost ready.', 'wangguard-registration-add-on')."</strong> ". __('You need to upgrade <a href="http://wordpress.org/extend/plugins/wangguard/">WangGuard</a> to version 1.6-RC1 or higher to use this plugin.', 'wangguard-registration-add-on')."</p></div>
			";
	}
}
add_action('admin_notices', 'wangguard_limit_domain_regisration_notices');


// Save the new settings
function wangguard_save_limit_domain_regisration_fileds(){

			//Save banned domains

			$wangguardnewbanneddomains = $_POST['wangguard_banned_email_domains'];
			$wanglisttoarraybanned = explode("\n", maybe_serialize(strtolower($wangguardnewbanneddomains)));
			update_site_option('wangguard_banned_email_domains', $wanglisttoarraybanned);
			                    
            //Save limited domains
			$wangguardnewlimiteddomains = $_POST['wangguard_limited_email_domains'];
            $wangguardemailstoarray = explode("\n", strtolower($wangguardnewlimiteddomains));                      
			update_site_option('wangguard_limited_email_domains', $wangguardemailstoarray);
                    
}
add_action('wangguard_save_setting_option', 'wangguard_save_limit_domain_regisration_fileds');


//Add setting to WangGuard Setting page
function wangguard_limit_domain_regisration_fileds() { ?>
					<h3>Limited-Banned Domains</h3>
					<p>
						<label for="wangguard_limited_email_domains"><?php _e( 'Limited Email Domains Registrations. One per line', 'wangguard-registration-add-on' ) ?></label><br />

						<?php $wangguard_limited_email_domains = get_site_option( 'wangguard_limited_email_domains' );
						$wangguard_limited_email_domains = str_replace( ' ', "\n", $wangguard_limited_email_domains ); ?>
						<textarea name="wangguard_limited_email_domains" id="limited_email_domains" cols="45" rows="5"><?php echo esc_textarea( $wangguard_limited_email_domains == '' ? '' : implode( "\n", (array) $wangguard_limited_email_domains ) ); ?></textarea>
					</p>
					
					<p>
						<label for="wangguard_banned_email_domains"><?php _e('Banned Email Domains. One domain per line.', 'wangguard-registration-add-on') ?></label><br />
				<td>
					<textarea name="wangguard_banned_email_domains" id="wangguard_banned_email_domains" cols="45" rows="5"><?php echo esc_textarea( get_site_option( 'wangguard_banned_email_domains' ) == '' ? '' : implode( "\n", (array) get_site_option( 'wangguard_banned_email_domains' ) ) ); ?></textarea>
					</p>
				<?php
}
add_action('wangguard_setting','wangguard_limit_domain_regisration_fileds' );

/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS REGISTRATION FORM BEGINS **/
/********************************************************************/

function wangguard_limit_domain_registration_blocked_add_on($user_name, $user_email, $errors){
        
        $blocked = wangguard_is_domain_blocked_add_on($user_email);
		
		if ($blocked) {
			$errors->add('user_email',   __('<strong>ERROR</strong>: Domain not allowed.', 'wangguard-registration-add-on'));
			return;
        }       
}

function wangguard_limit_domain_registration_allowed_add_on($user_name, $user_email, $errors){
       $domains = array_filter(get_site_option( 'wangguard_limited_email_domains'));
       		if (!empty($domains)){
			   				$allowed = wangguard_is_domain_allowed_add_on($user_email);
			   				if ($allowed == false) {
				   					$errors->add('user_email',   __('<strong>ERROR</strong>: Domain not whitelisted.', 'wangguard-registration-add-on'));
				   					return;
				   				}     
				   	}
}
//add_action('wangguard_wp_signup_validate', 'wangguard_limit_domain_regisration_blocked_allowed_add_on');
add_action('register_post', 'wangguard_limit_domain_registration_blocked_add_on',10,3);
add_action('register_post', 'wangguard_limit_domain_registration_allowed_add_on',10,3);

/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS REGISTRATION FORM ENDS **/
/********************************************************************/

/********************************************************************/
/*** ADD MESSAGE IN THE WORDPRESS MULTISITE REGISTRATION FORM BEGINS **/
/********************************************************************/

if (is_multisite()) {
		require( dirname( __FILE__ ) . '/wangguard-limited-registration-domain-wpmu.php' );
	}

/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS BUDDYPRESS REGISTRATION FORM BEGINS **/
/********************************************************************/

function wangguard_bp_registration_domain_code() {
    require( dirname( __FILE__ ) . '/wangguard-limited-registration-domain-bp.php' );
}
add_action( 'bp_include', 'wangguard_bp_registration_domain_code' );



/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS BUDDYPRESS REGISTRATION FORM ENDS **/
/********************************************************************/

/********************************************************************/
/*** CHECK DOMAINS IN THE WOOCOMMERCE MY ACCOUNT FORM BEGINS **/
/********************************************************************/
function wangguard_limit_domain_registration_blocked_woocommerce_add_on($user_name, $email, $errors){

		$user_email = $_POST['email'];
        $blocked = wangguard_is_domain_blocked_add_on($user_email);
		if ($blocked) {
			$errors->add('user_email',   __('<strong>ERROR</strong>: Domain not allowed.', 'wangguard-registration-add-on'));
			return;
        }       
}

function wangguard_limit_domain_registration_allowed_woocommerce_add_on($user_name, $email, $errors){
        
       $domains = array_filter(get_site_option( 'wangguard_limited_email_domains'));
       		if (!empty($domains)){
		   		$user_email = $_POST['email'];
		   		$allowed = wangguard_is_domain_allowed_add_on($user_email);
		   		if ($allowed == false) {
			   		$errors->add('user_email',   __('<strong>ERROR</strong>: Domain not allowed.', 'wangguard-registration-add-on'));
			   		return;
			   	}       
			 }
}
if (get_option('woocommerce_enable_myaccount_registration')=='yes') add_action('woocommerce_before_customer_login_form', 'wangguard_wpmu_signup_message');
/********************************************************************/
/*** CHECK DOMAINS IN THE WOOCOMMERCE MY ACCOUNT FORM ENDS **/
/********************************************************************/

/********************************************************************/
/*** LOOK FOR DOMAINS BEGINS **/
/********************************************************************/

function wangguard_is_domain_blocked_add_on($email){
	$parts = explode("@", $email);
	$domain = strtolower($parts[1]);
	$arraybanneddomains = get_site_option('wangguard_banned_email_domains'); 
	foreach ($arraybanneddomains as $key => $value) {$arraybanneddomains[$key] = trim ($value);} 
	$search_for = $domain; 
	if (array_search ($search_for, $arraybanneddomains, true)===false) {
				return false;
				} else {
					return true;
				}
}

function wangguard_is_domain_allowed_add_on($email){
	$parts = explode("@", $email);
	$domain = strtolower($parts[1]);
	$arrayalloweddomains = get_site_option('wangguard_limited_email_domains'); 
	foreach ($arrayalloweddomains as $key => $value) {$arrayalloweddomains[$key] = trim ($value);} 
	$search_for = $domain; 
	if (array_search ($search_for, $arrayalloweddomains, true)===false) {
				return false;
				} else {
					return true;
				}
}

/********************************************************************/
/*** LOOK FOR DOMAINS ENDS **/
/********************************************************************/
?>