<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class miniifbox_cls_dbquery {

	public static function miniifbox_count($id = 0) {

		global $wpdb;
		$result = '0';
		if(!is_numeric($id)) { 
			return $result;
		}
		
		if($id <> "" && $id > 0) {
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "miniifbox WHERE box_id = %d", array($id));
		} 
		else {
			$sSql = "SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "miniifbox";
		}
		
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function miniifbox_select_bygroup($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "miniifbox";

		if($group <> "") {
			$sSql = $sSql . " WHERE box_group = %s";
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
		}
		
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function miniifbox_select() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "miniifbox";
		$sSql = $sSql . " Order by box_id desc, box_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function miniifbox_select_byid($id = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "miniifbox";

		if($id <> "") {
			$sSql = $sSql . " WHERE box_id = %d LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($id));
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		
		return $arrRes;
	}
	
	public static function miniifbox_select_shortcode($id = "", $group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "miniifbox WHERE box_status = 'Yes'";
		$sSql .= " AND ( box_start <= NOW() or box_start = '0000-00-00' )";
		$sSql .= " AND ( box_end >= NOW() or box_end = '0000-00-00' )";
		
		if($id <> "" && $id <> "0") {
			$sSql .= " AND box_id = %d LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($id));
		}
		elseif($group <> "") {
			$sSql .= " AND box_group = %s Order by rand() LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
		}
		
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		
		return $arrRes;
	}
	
	public static function miniifbox_group() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT distinct(box_group) FROM " . $wpdb->prefix . "miniifbox order by box_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}

	public static function miniifbox_delete($id = "") {

		global $wpdb;

		if($id <> "") {
			$sSql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "miniifbox WHERE box_id = %d LIMIT 1", $id);
			$wpdb->query($sSql);
		}

		return true;
	}

	public static function miniifbox_insert($data = array()) {

		global $wpdb;
		
		$sql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "miniifbox (
			box_title, 
			box_srcdoc,
			box_srclink,
			box_group,
			box_width, 
			box_height, 
			box_style,
			box_status,
			box_start,
			box_end
			) 
			VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", 
			array(
			$data["box_title"], 
			$data["box_srcdoc"], 
			$data["box_srclink"],
			$data["box_group"], 
			$data["box_width"], 
			$data["box_height"], 
			$data["box_style"], 
			$data["box_status"],
			$data["box_start"],
			$data["box_end"]
			));

		$wpdb->query($sql);
		return "inserted";
	}
	
	public static function miniifbox_update($data = array()) {

		global $wpdb;
		
		$sSql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "miniifbox SET 
			box_title = %s, 
			box_srcdoc = %s, 
			box_srclink = %s, 
			box_group = %s, 
			box_width = %s, 
			box_height = %s, 
			box_style = %s, 
			box_status = %s, 
			box_start = %s, 
			box_end = %s 
			WHERE box_id = %d LIMIT 1", 
			array(
			$data["box_title"], 
			$data["box_srcdoc"], 
			$data["box_srclink"],
			$data["box_group"], 
			$data["box_width"], 
			$data["box_height"], 
			$data["box_style"], 
			$data["box_status"], 
			$data["box_start"], 
			$data["box_end"], 
			$data["box_id"]
			));

		$wpdb->query($sSql);
		return "update";
	}

	public static function miniifbox_default() {

		$count = miniifbox_cls_dbquery::miniifbox_count($id = 0);
		if($count == 0){
			
			$today = date("Y-m-d");
			$text1 = 'Mini iframe box wordpress plugin will create a mini iframe window that contains another html content or web page. ';
			$text1 .= 'We can use this iframe box in the widget or short code. In plugin admin, we have option to add and edit iframe content and web page link. '; 
			$text1 .= 'Also we have option to configure start and end date for the iframe window. Additionally, we have option to enter iframe style in the admin. ';	
			
			$data['box_title'] = 'This is sample mini iframe title 1.';
			$data['box_srcdoc'] = $text1;
			$data['box_srclink'] = '';
			$data['box_group'] = 'General';
			$data['box_width'] = '100';
			$data['box_height'] = '100';
			$data['box_style'] = 'border:10px solid #f5f8fa;font-family: "Open Sans", Arial, sans-serif;';
			$data['box_status'] = 'Yes';
			$data['box_start'] = $today;
			$data['box_end'] = '9999-12-31';
			miniifbox_cls_dbquery::miniifbox_insert($data);
			
			$box_srclink = get_home_url();
			$data1['box_title'] = 'This is sample mini iframe title 2.';
			$data1['box_srcdoc'] = '';
			$data1['box_srclink'] = $box_srclink;
			$data1['box_group'] = 'General';
			$data1['box_width'] = '100';
			$data1['box_height'] = '200';
			$data1['box_style'] = 'border:1px solid #000000';
			$data1['box_status'] = 'Yes';
			$data1['box_start'] = $today;
			$data1['box_end'] = '9999-12-31';
			miniifbox_cls_dbquery::miniifbox_insert($data1);

		}
	}
	
	public static function miniifbox_common_text($value) {
		
		$returnstring = "";
		switch ($value) {
			case "Yes":
				$returnstring = '<span style="color:#006600;">Yes</span>';
				break;
			case "No":
				$returnstring = '<span style="color:#FF0000;">No</span>';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
}