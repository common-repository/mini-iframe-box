<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class miniifbox_cls_registerhook {
	public static function miniifbox_activation() {
	
		global $wpdb;

		add_option('mini-iframe-box', "1.0");

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();
	
		$miniifbox_default_tables = "CREATE TABLE {$wpdb->prefix}miniifbox (
										box_id INT unsigned NOT NULL AUTO_INCREMENT,
										box_title VARCHAR(1024) NOT NULL default '',
										box_srcdoc TEXT,
										box_srclink VARCHAR(1024) NOT NULL default '',
										box_group VARCHAR(20) NOT NULL default 'General',
										box_width VARCHAR(4) NOT NULL default '100',
										box_height VARCHAR(4) NOT NULL default '200',
										box_style VARCHAR(1024) NOT NULL default 'border:1px solid black',
										box_status VARCHAR(3) NOT NULL default 'Yes',
										box_start date NOT NULL DEFAULT '0000-00-00', 
										box_end date NOT NULL DEFAULT '9999-12-31',
										PRIMARY KEY (box_id)
									) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $miniifbox_default_tables );
		
		$miniifbox_default_tablesname = array( 'miniifbox' );
	
		$miniifbox_has_errors = false;
		$miniifbox_missing_tables = array();
		foreach($miniifbox_default_tablesname as $table_name) {
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $wpdb->prefix.$table_name . "'")) != strtoupper($wpdb->prefix.$table_name)) {
				$miniifbox_missing_tables[] = $wpdb->prefix.$table_name;
			}
		}

		if($miniifbox_missing_tables) {
			$errors[] = __( 'These tables could not be created on installation ' . implode(', ',$miniifbox_missing_tables), 'mini-iframe-box' );
			$miniifbox_has_errors = true;
		}
		
		if($miniifbox_has_errors) {
			wp_die( __( $errors[0] , 'mini-iframe-box' ) );
			return false;
		} 
		else {
			miniifbox_cls_dbquery::miniifbox_default();
		}
		
		return true;
	}

	public static function miniifbox_deactivation() {
		// do not generate any output here
	}

	public static function miniifbox_adminoptions() {
	
		global $wpdb;
		$current_page = isset($_GET['ac']) ? sanitize_text_field($_GET['ac']) : '';
		
		switch($current_page) {
			case 'edit':
				require_once(MINIIFBOX_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-edit.php');
				break;
			case 'add':
				require_once(MINIIFBOX_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-add.php');
				break;
			default:
				require_once(MINIIFBOX_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-show.php');
				break;
		}
	}
	
	public static function miniifbox_addtomenu() {
	
		if (is_admin()) {
			add_options_page( __('Mini iframe box', 'mini-iframe-box'), 
								__('Mini iframe box', 'mini-iframe-box'), 'manage_options', 
									'mini-iframe-box', array( 'miniifbox_cls_registerhook', 'miniifbox_adminoptions' ) );
		}
	}
	
	public static function miniifbox_adminscripts() {
	
		if(!empty($_GET['page'])) {
			switch (sanitize_text_field($_GET['page'])) {
				case 'mini-iframe-box':
					wp_register_script( 'miniifbox-adminscripts', plugin_dir_url( __DIR__ ) . '/pages/setting.js', '', '', true );
					wp_enqueue_script( 'miniifbox-adminscripts' );
					$miniifbox_select_params = array(
						'box_title'  		=> __( 'Please enter iframe box title.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_text'  		=> __( 'Please enter iframe content or iframe url.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_group'  		=> __( 'Please select group for this iframe box.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_width'  		=> __( 'Please enter width of the iframe box (in percentage), only number.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_height'  		=> __( 'Please enter height of the iframe box (in pixel), only number.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_status'  		=> __( 'Please select display status of this iframe box.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_start'  		=> __( 'Please enter display start date of this iframe box, format YYYY-MM-DD.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_end'  			=> __( 'Please enter display end date of this iframe box, format YYYY-MM-DD.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_style'  		=> __( 'Please enter style for this iframe box (Optional).', 'miniifbox-select', 'mini-iframe-box' ),
						'box_numletters'  	=> __( 'Please input numeric and letters only.', 'miniifbox-select', 'mini-iframe-box' ),
						'box_delete'  		=> __( 'Do you want to delete this record?', 'miniifbox-select', 'mini-iframe-box' ),
					);
					wp_localize_script( 'miniifbox-adminscripts', 'miniifbox_adminscripts', $miniifbox_select_params );
					break;
			}
		}
	}
}

class miniifbox_cls_shortcode {
	public function __construct() {
	}
	
	public static function miniifbox_shortcode( $atts ) {
		ob_start();
		if (!is_array($atts)) {
			return '';
		}
		
		//[mini-iframe-box group="General"]
		//[mini-iframe-box id="1"]
		$atts = shortcode_atts( array(
				'group'	=> '',
				'id'	=> ''
			), $atts, 'mini-iframe-box' );

		$group 	= isset($atts['group']) ? sanitize_text_field($atts['group']) : '';
		$id 	= isset($atts['id']) ? intval($atts['id']) : '';

		$data = array(
			'group' => $group,
			'id' 	=> $id
		);
		
		self::miniifbox_render( $data );

		return ob_get_clean();
	}
	
	public static function miniifbox_render( $data = array() ) {	
		
		$ifbox = "";
		
		if(count($data) == 0) {
			return $ifbox;
		}

		$id = intval($data['id']);
		$group 	= sanitize_text_field($data['group']);

		$data = miniifbox_cls_dbquery::miniifbox_select_shortcode($id, $group);
		if(count($data) > 0 ) {
			$txt = $data['box_srcdoc'];
			$url = $data['box_srclink'];
			$w = $data['box_width'];
			$h = $data['box_height'];
			$s = $data['box_style'];
			
			if($txt <> "" && strlen($txt) > 0) {
				$txt =  trim($txt);
				$txt = do_shortcode($txt);
			}
			
			$ifbox = '<iframe';
			
			if($txt <> "" && strlen($txt) > 3) {
				$ifbox .= ' srcdoc="' . stripslashes(htmlspecialchars($txt)) . '"';
			}
			else {
				if($url <> "") {
					$ifbox .= ' src="' . $url . '"';
				}
			}
			
			$ifbox .= ' style="' . stripslashes(htmlspecialchars($s)) . '" width="' . $w . '%" height="' . $h . 'px" target="cwindow">';			
			$ifbox .= '</iframe>';
		}
		echo $ifbox;
	}
}
?>