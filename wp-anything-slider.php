<?php
/*
Plugin Name: Wp anything slider
Plugin URI: http://www.gopiplus.com/work/2012/04/20/wordpress-plugin-wp-anything-slider/
Description: Wp anything slider plug-in let you to create the sliding slideshow gallery into your posts and pages. In the admin we have Tiny MCE HTML editor to add, update the content. using this HTML editor we can add HTML text and can upload the images and video files.
Author: Gopi Ramasamy
Version: 9.2
Author URI: http://www.gopiplus.com/work/2012/04/20/wordpress-plugin-wp-anything-slider/
Donate link: http://www.gopiplus.com/work/2012/04/20/wordpress-plugin-wp-anything-slider/
Tags: Wordpress, plugin, slider
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-anything-slider
Domain Path: /languages
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb, $wp_version;
define("WP_ANYTHING_SETTINGS", $wpdb->prefix . "wpanything_settings");
define("WP_ANYTHING_CONTENT", $wpdb->prefix . "wpanything_content");
define('Wp_wpanything_FAV', 'http://www.gopiplus.com/work/2012/04/20/wordpress-plugin-wp-anything-slider/');

if ( ! defined( 'WP_wpanything_BASENAME' ) )
	define( 'WP_wpanything_BASENAME', plugin_basename( __FILE__ ) );
	
if ( ! defined( 'WP_wpanything_PLUGIN_NAME' ) )
	define( 'WP_wpanything_PLUGIN_NAME', trim( dirname( WP_wpanything_BASENAME ), '/' ) );
	
if ( ! defined( 'WP_wpanything_PLUGIN_URL' ) )
	define( 'WP_wpanything_PLUGIN_URL', WP_PLUGIN_URL . '/' . WP_wpanything_PLUGIN_NAME );
	
if ( ! defined( 'WP_wpanything_ADMIN_URL' ) )
	define( 'WP_wpanything_ADMIN_URL', get_option('siteurl') . '/wp-admin/options-general.php?page=wp-anything-slider' );

function wpanything_install() 
{
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_ANYTHING_SETTINGS . "'") != WP_ANYTHING_SETTINGS) 
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". WP_ANYTHING_SETTINGS . "` (
			  `wpanything_sid` int(11) NOT NULL auto_increment,
			  `wpanything_sname` VARCHAR( 10 ) NOT NULL,
			  `wpanything_sdirection` VARCHAR( 12 ) NOT NULL default 'scrollLeft',
			  `wpanything_sspeed` int(11) NOT NULL default '700',
			  `wpanything_stimeout` int(11) NOT NULL default '5000',
			  `wpanything_srandom` VARCHAR( 3 ) NOT NULL default 'YES',
			  `wpanything_sextra` VARCHAR( 100 ) NOT NULL,
			  PRIMARY KEY  (`wpanything_sid`) )
			");
		$iIns = "INSERT INTO `". WP_ANYTHING_SETTINGS . "` (`wpanything_sname`)"; 
		
		for($i=1; $i<=10; $i++)
		{
			$sSql = $iIns . " VALUES ('SETTING".$i."')";
			$wpdb->query($sSql);
		}
	}
	if($wpdb->get_var("show tables like '". WP_ANYTHING_CONTENT . "'") != WP_ANYTHING_CONTENT) 
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". WP_ANYTHING_CONTENT . "` (
			  `wpanything_cid` int(11) NOT NULL auto_increment,
			  `wpanything_ctitle` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
			  `wpanything_cstartdate` datetime NOT NULL default '2012-01-01 00:00:00',
			  `wpanything_cenddate` datetime NOT NULL default '2020-12-30 00:00:00',
			  `wpanything_csetting` VARCHAR( 12 ) NOT NULL,
			  PRIMARY KEY  (`wpanything_cid`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		$iIns = "INSERT INTO `". WP_ANYTHING_CONTENT . "` (`wpanything_ctitle`, `wpanything_csetting`)"; 
		
		for($i=1; $i<=6; $i++)
		{
			if($i >= 1 and $i<=2) { $j = 1; } elseif ($i >= 3 and $i<=4) { $j = 2; } else { $j = 3; }
			$sSql = $iIns . " VALUES ('Lorem Ipsum is simply dummy text of the printing industry ".$i.".', 'SETTING".$j."')";
			$wpdb->query($sSql);
		}
	}
	add_option('wpanything_title', "Announcement");
}

function wpanything_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'add':
			include('pages/content-add.php');
			break;
		case 'edit':
			include('pages/content-edit.php');
			break;
		case 'editcycle':
			include('pages/cycle-setting-edit.php');
			break;
		case 'showcycle':
			include('pages/cycle-setting-show.php');
			break;
		default:
			include('pages/content-show.php');
			break;
	}
}

function wpanything_shortcode( $atts ) 
{
	global $wpdb;

	// [wp-anything-slider setting="SETTING1"]	
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$setting = $atts['setting'];
	
	$wpcycle = "";
	$sSql = "select wpanything_sid, wpanything_sname, wpanything_sdirection,";
	$sSql = $sSql . " wpanything_sspeed, wpanything_stimeout, wpanything_srandom from ". WP_ANYTHING_SETTINGS ." where 1=1";
	$sSql = $sSql . " and wpanything_sname = %s ";
	
	$sSql = $wpdb->prepare($sSql, strtoupper($setting));
	
	$wpcycletxt_settings = $wpdb->get_results($sSql);
	if ( ! empty($wpcycletxt_settings) ) 
	{
			$settings = $wpcycletxt_settings[0];
			$wpanything_sname = esc_html($settings->wpanything_sname); 
			$wpanything_sdirection = esc_html($settings->wpanything_sdirection); 
			$wpanything_sspeed = esc_html($settings->wpanything_sspeed); 
			$wpanything_stimeout = esc_html($settings->wpanything_stimeout); 
			$wpanything_srandom = esc_html($settings->wpanything_srandom); 
	}
	$wpcycle = $wpcycle . '<div id="WP-ANYTHING-'.$wpanything_sname.'">';
	$sSql = "select wpanything_cid, wpanything_ctitle from ". WP_ANYTHING_CONTENT ." where 1=1";
	//$sSql = $sSql . " and (`wpanything_cstartdate` <= NOW() and `wpanything_cenddate` >= NOW())";
	$sSql = $sSql . " and wpanything_csetting = %s ";
	
	$sSql = $wpdb->prepare($sSql, strtoupper($setting));
	
	$wpcycletxt = $wpdb->get_results($sSql);
	if ( ! empty($wpcycletxt) ) 
	{
		foreach ( $wpcycletxt as $text ) 
		{
			$wpanything_ctitle = stripslashes($text->wpanything_ctitle);
            $wpcycle = $wpcycle . '<div id="anything">' . $wpanything_ctitle . '</div>';
		}
	}

	$wpcycle = $wpcycle . '</div>';
	$wpcycle = $wpcycle . '<script type="text/javascript">';
    $wpcycle = $wpcycle . 'jQuery(function() {';
	$wpcycle = $wpcycle . "jQuery('#WP-ANYTHING-".strtoupper($setting)."').cycle({fx: '".$wpanything_sdirection."',speed: " . $wpanything_sspeed . ",timeout: " . $wpanything_stimeout . "";
	$wpcycle = $wpcycle . '});';
	$wpcycle = $wpcycle . '});';
	$wpcycle = $wpcycle . '</script>';
	
	return $wpcycle;
}

function wpanything_add_to_menu() 
{
	if (is_admin()) 
	{
		add_options_page( __('Wp Anything Slider', 'wp-anything-slider'), 
				__('Wp Anything Slider', 'wp-anything-slider'), 'manage_options', 'wp-anything-slider', 'wpanything_admin_options' );
	}
}

function wpanything_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'jquery.cycle.all.latest', WP_wpanything_PLUGIN_URL.'/js/jquery.cycle.all.latest.js');
		wp_enqueue_style( 'wp-anything-slider', WP_wpanything_PLUGIN_URL.'/wp-anything-slider.css');
	}	
}

function wpanything_deactivation() 
{
	// No action required.
}

function wpanything_textdomain() 
{
	  load_plugin_textdomain( 'wp-anything-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function wpanything_adminscripts() 
{
	if( !empty( $_GET['page'] ) ) 
	{
		switch ( $_GET['page'] ) 
		{
			case 'wp-anything-slider':
				wp_register_script( 'wp-anything-adminscripts', WP_wpanything_PLUGIN_URL . '/pages/setting.js', '', '', true );
				wp_enqueue_script( 'wp-anything-adminscripts' );
				$wp_anything_adminscripts_params = array(
					'wpanything_sspeed'  	=> __( 'Please enter the slider speed, only number.', 'wp-anything-select', 'wp-anything-slider' ),
					'wpanything_stimeout'  	=> __( 'Please enter the slider timeout, only number.', 'wp-anything-select', 'wp-anything-slider' ),
					'wpanything_sdirection' => __( 'Please select the slider direction', 'wp-anything-select', 'wp-anything-slider' ),
					'wpanything_content'  	=> __( 'Please enter the content.', 'wp-anything-select', 'wp-anything-slider' ),
					'wpanything_csetting'  	=> __( 'Please select the setting name.', 'wp-anything-select', 'wp-anything-slider' ),
					'wpanything_delete'  	=> __( 'Do you want to delete this record?', 'wp-anything-select', 'wp-anything-slider' ),
				);
				wp_localize_script( 'wp-anything-adminscripts', 'wp_anything_adminscripts', $wp_anything_adminscripts_params );
				break;
		}
	}
}

add_action('plugins_loaded', 'wpanything_textdomain');
add_shortcode( 'wp-anything-slider', 'wpanything_shortcode' );
add_action('admin_menu', 'wpanything_add_to_menu');
add_action('wp_enqueue_scripts', 'wpanything_add_javascript_files');
register_activation_hook(__FILE__, 'wpanything_install');
register_deactivation_hook(__FILE__, 'wpanything_deactivation');
add_action('admin_enqueue_scripts', 'wpanything_adminscripts');
?>