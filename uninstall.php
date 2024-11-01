<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option('wpanything_title');
 
// for site options in Multisite
delete_site_option('wpanything_title');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpanything_settings");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpanything_content");