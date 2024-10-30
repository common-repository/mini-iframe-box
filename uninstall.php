<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option('mini-iframe-box');
 
// for site options in Multisite
delete_site_option('mini-iframe-box');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}miniifbox");