<?php
/*
Plugin Name: Mini iframe box
Plugin URI: http://www.gopiplus.com/work/2020/04/12/mini-iframe-box-wordpress-plugin/
Description: A mini iframe window box that contains another html content or web page url.
Author: Gopi Ramasamy
Version: 1.4
Author URI: http://www.gopiplus.com/work/about/
Donate link: http://www.gopiplus.com/work/2020/04/12/mini-iframe-box-wordpress-plugin/
Tags: plugin, iframe, text, box
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mini-iframe-box
Domain Path: /languages
*/

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

if(!defined('MINIIFBOX_DIR')) 
	define('MINIIFBOX_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if ( ! defined( 'MINIIFBOX_ADMIN_URL' ) )
	define( 'MINIIFBOX_ADMIN_URL', admin_url() . 'options-general.php?page=mini-iframe-box' );

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'miniifbox-register.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'miniifbox-query.php');

function miniifbox_textdomain() {
	  load_plugin_textdomain( 'mini-iframe-box', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_shortcode( 'mini-iframe-box', array( 'miniifbox_cls_shortcode', 'miniifbox_shortcode' ) );

add_action('plugins_loaded', 'miniifbox_textdomain');
add_action('admin_enqueue_scripts', array('miniifbox_cls_registerhook', 'miniifbox_adminscripts'));
add_action('admin_menu', array('miniifbox_cls_registerhook', 'miniifbox_addtomenu'));

register_activation_hook(MINIIFBOX_DIR . 'mini-iframe-box.php', array('miniifbox_cls_registerhook', 'miniifbox_activation'));
register_deactivation_hook(MINIIFBOX_DIR . 'mini-iframe-box.php', array('miniifbox_cls_registerhook', 'miniifbox_deactivation'));
?>