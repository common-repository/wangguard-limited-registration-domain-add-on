<?php
function wangguard_limit_domain_registration_mu_blocked_add_on($result) {

		   	$user_email = $_POST['user_email'];

		//check domain against the list of selected blocked domains
		$blocked = wangguard_is_domain_blocked_add_on($user_email);
		
		if ($blocked) {
			$result['errors']->add('user_email',   __('<strong>ERROR</strong>: Domain not allowed.', 'wangguard'));
		}
		
	return $result; 
}

function wangguard_limit_domain_registration_mu_allowed_add_on($result) {

		$domains = array_filter(get_site_option( 'wangguard_limited_email_domains'));
       		if (!empty($domains)){
		   		$user_email = $_POST['user_email'];

		   		//check domain against the list of selected blocked domains
		   		$allowed = wangguard_is_domain_allowed_add_on($user_email);
		
		   		if ($allowed == false) {
			   		$result['errors']->add('user_email',   __('<strong>ERROR</strong>: Domain not whitelisted.', 'wangguard'));
			   		}
		
			   	return $result;
			  }
}
if ( get_site_option( 'wangguard_banned_email_domains') && get_site_option( 'wangguard_limited_email_domains') ){
	$domanbanned = array_filter(get_site_option( 'wangguard_banned_email_domains'));
	$domainslimited = array_filter(get_site_option( 'wangguard_limited_email_domains'));
       		if (!empty($domanbanned)){
		   			add_filter('wpmu_validate_user_signup', 'wangguard_limit_domain_registration_mu_blocked_add_on', 100);
		   		}
		   	if (!empty($domainslimited)){
			   		add_filter('wpmu_validate_user_signup', 'wangguard_limit_domain_registration_mu_allowed_add_on', 110);
			   	}
}
?>