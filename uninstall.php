<?php
/**
 * Uninstall WangGuard Limited Registration Domain Add-On
 * @version     1.0
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();


//Remove Options used by this Add_on

delete_site_option('wangguard_limited_email_domains');
delete_site_option('wangguard_banned_email_domains');

?>