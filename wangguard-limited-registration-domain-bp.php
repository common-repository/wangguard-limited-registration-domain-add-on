<?PHP
/**
 * Add BP DOMAIN CHECK
 *
 * @author 		WangGuard
 * @package 	WangGuard/Add-on
 * @version     1.0
 */


//We need to remove the WMPU filters because with BP 2.0 breaks the signup page.

remove_filter('wpmu_validate_user_signup', 'wangguard_limit_domain_registration_mu_blocked_add_on', 100);
remove_filter('wpmu_validate_user_signup', 'wangguard_limit_domain_registration_mu_allowed_add_on', 110);

function wangguard_limit_domain_registration_bp_blocked_add_on(){
		global $bp;
		
		$signup_email = $_REQUEST['signup_email'];
        $blocked = wangguard_is_domain_blocked_add_on($signup_email);
   		if ($blocked) {
				$bp->signup->errors['signup_email'] = wangguard_fix_bp_slashes_maybe( __("<strong>ERROR</strong>: Domain not allowed.", 'wangguard'));
        }     
        if (isset ($bp->signup->errors['signup_email']))$bp->signup->errors['signup_email'] = wangguard_fix_bp_slashes_maybe($bp->signup->errors['signup_email']);  
}

function wangguard_limit_domain_registration_allowed_bp_add_on(){
		global $bp;
        
        $domains = array_filter(get_site_option( 'wangguard_limited_email_domains'));
       		if (!empty($domains)){
		   		$signup_email = $_REQUEST['signup_email'];
		   		$allowed = wangguard_is_domain_allowed_add_on($signup_email);
		   		if ($allowed == false) {
					$bp->signup->errors['signup_email'] = wangguard_fix_bp_slashes_maybe(__("<strong>ERROR</strong>: Domain not allowed.", 'wangguard'));
				}
				if (isset ($bp->signup->errors['signup_email']))$bp->signup->errors['signup_email'] = wangguard_fix_bp_slashes_maybe($bp->signup->errors['signup_email']);      
			}
}
add_action('bp_signup_validate', 'wangguard_limit_domain_registration_bp_blocked_add_on',10,3);
add_action('bp_signup_validate', 'wangguard_limit_domain_registration_allowed_bp_add_on',10,3);
?>