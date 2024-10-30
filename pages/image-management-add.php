<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$box_errors = array();
$box_success = '';
$box_error_found = false;

$form = array(
	'box_title' => '',
	'box_srcdoc' => '',
	'box_srclink' => '',
	'box_group' => '',
	'box_width' => '',
	'box_height' => '',
	'box_style' => '',
	'box_status' => '',
	'box_start' => '',
	'box_end' => ''
);

if (isset($_POST['box_form_submit']) && sanitize_text_field($_POST['box_form_submit']) == 'yes') {
	check_admin_referer('box_form_add');
	
	$form['box_title'] = isset($_POST['box_title']) ? sanitize_text_field($_POST['box_title']) : '';
	if ($form['box_title'] == '') {
		$box_errors[] = __('Please enter iframe box title.', 'mini-iframe-box');
		$box_error_found = true;
	}

	$form['box_srcdoc'] = isset($_POST['box_srcdoc']) ? wp_filter_post_kses($_POST['box_srcdoc']) : '';
	
	$form['box_srclink'] = isset($_POST['box_srclink']) ? sanitize_text_field($_POST['box_srclink']) : '';
	if ($form['box_srclink'] <> '') {
		$form['box_srclink'] = esc_url_raw( $form['box_srclink'] );
	}
	
	$form['box_group'] = isset($_POST['box_group']) ? sanitize_text_field($_POST['box_group']) : '';
	if ($form['box_group'] == '') {
		$form['box_group'] = isset($_POST['box_group_txt']) ? sanitize_text_field($_POST['box_group_txt']) : '';
	}
	if ($form['box_group'] == '') {
		$box_errors[] = __('Please select group for this iframe box.', 'mini-iframe-box');
		$box_error_found = true;
	}
	
	$form['box_width'] = isset($_POST['box_width']) ? intval(sanitize_text_field($_POST['box_width'])) : '';
	if ($form['box_width'] == '') {
		$box_errors[] = __('Please enter width of the iframe box (in percentage), only number.', 'mini-iframe-box');
		$box_error_found = true;
	}
	
	$form['box_height'] = isset($_POST['box_height']) ? intval(sanitize_text_field($_POST['box_height'])) : '';
	if ($form['box_height'] == '') {
		$box_errors[] = __('Please enter height of the iframe box (in pixel), only number.', 'mini-iframe-box');
		$box_error_found = true;
	}
	
	$form['box_style'] = isset($_POST['box_style']) ? wp_filter_post_kses($_POST['box_style']) : '';
	$form['box_status'] = isset($_POST['box_status']) ? sanitize_text_field($_POST['box_status']) : '';
	$form['box_start'] = isset($_POST['box_start']) ? sanitize_text_field($_POST['box_start']) : '0000-00-00';
	$form['box_end'] = isset($_POST['box_end']) ? sanitize_text_field($_POST['box_end']) : '9999-12-31';
	
	if ($box_error_found == false)
	{
		$status = miniifbox_cls_dbquery::miniifbox_insert($form);
		if($status == 'inserted') {
			$box_success = __('New details was successfully added.', 'mini-iframe-box');
		}
		else {
			$box_errors[] = __('Oops, something went wrong. try again.', 'mini-iframe-box');
			$box_error_found = true;
		}
		
		$form = array(
			'box_title' => '',
			'box_srcdoc' => '',
			'box_srclink' => '',
			'box_group' => '',
			'box_width' => '',
			'box_height' => '',
			'box_style' => '',
			'box_status' => '',
			'box_start' => '',
			'box_end' => ''
		);
	}
}

if ($box_error_found == true && isset($box_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $box_errors[0]; ?></strong></p></div><?php
}
if ($box_error_found == false && strlen($box_success) > 0) {
	?><div class="updated fade"><p><strong><?php echo $box_success; ?>
	<a href="<?php echo MINIIFBOX_ADMIN_URL; ?>"><?php _e('Click here', 'mini-iframe-box'); ?></a> <?php _e('to view the details', 'mini-iframe-box'); ?>
	</strong></p></div><?php
}
?>

<div class="form-wrap">
	<h1 class="wp-heading-inline"><?php _e('Add', 'mini-iframe-box'); ?></h1>
	<form name="box_form" method="post" action="#" onsubmit="return _miniifbox_submit()" >
	  
	  <label><strong><?php _e('Title', 'mini-iframe-box'); ?></strong></label>
      <input name="box_title" type="text" id="box_title" value="" size="60" maxlength="1024" />
      <p><?php _e('Please enter iframe box title.', 'mini-iframe-box'); ?></p>
	  
      <label><strong><?php _e('iFrame content', 'mini-iframe-box'); ?></strong></label>
      <?php 
	  wp_editor("", "box_srcdoc"); 
	  ?>
      <p><?php _e('Please enter iframe content or iframe url.', 'mini-iframe-box'); ?></p>
	   
	  <label><strong><?php _e('iFrame url', 'mini-iframe-box'); ?></strong></label>
      <input name="box_srclink" type="text" id="box_srclink" value="" size="60" maxlength="1024" />
      <p><?php _e('Please enter iframe content or iframe url.', 'mini-iframe-box'); ?></p>
	  
      <label><strong><?php _e('Group', 'mini-iframe-box'); ?></strong></label>
		<select name="box_group" id="box_group">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$groups = array();
			$groups = miniifbox_cls_dbquery::miniifbox_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo stripslashes($group["box_group"]); ?>">
						<?php echo stripslashes($group["box_group"]); ?>
					</option>
					<?php
				}
			}
			?>
		</select>
		(or) 
	   	<input name="box_group_txt" type="text" id="box_group_txt" value="" maxlength="15" onkeyup="return _miniifbox_numericandtext(document.box_form.box_group_txt)" />
      <p><?php _e('Please select group for this iframe box.', 'mini-iframe-box'); ?></p>
	  
	  <label><strong><?php _e('Width', 'mini-iframe-box'); ?></strong></label>
      <input name="box_width" type="text" id="box_width" value="100" maxlength="3" />
      <p><?php _e('Please enter width of the iframe box (in percentage), only number.', 'mini-iframe-box'); ?></p>
	  
	  <label><strong><?php _e('Height', 'mini-iframe-box'); ?></strong></label>
      <input name="box_height" type="text" id="box_height" value="200" maxlength="3" />
      <p><?php _e('Please enter height of the iframe box (in pixel), only number.', 'mini-iframe-box'); ?></p>
	    
	  <label><strong><?php _e('Display status', 'mini-iframe-box'); ?></strong></label>
      <select name="box_status" id="box_status">
        <option value='Yes'>Yes</option>
        <option value='No'>No</option>
      </select>
      <p><?php _e('Please select display status of this iframe box.', 'mini-iframe-box'); ?></p>
	  
	  <label><strong><?php _e('Start date', 'mini-iframe-box'); ?></strong></label>
      <input name="box_start" type="text" id="box_start" value="<?php echo date("Y-m-d"); ?>" maxlength="10" />
      <p><?php _e('Please enter display start date of this iframe box, format YYYY-MM-DD.', 'mini-iframe-box'); ?></p>
	  
	  <label><strong><?php _e('End date', 'mini-iframe-box'); ?></strong></label>
      <input name="box_end" type="text" id="box_end" value="9999-12-31" maxlength="10" />
      <p><?php _e('Please enter display end date of this iframe box, format YYYY-MM-DD.', 'mini-iframe-box'); ?></p>
	  
	  <label><strong><?php _e('CSS style', 'mini-iframe-box'); ?></strong></label>
      <input name="box_style" type="text" id="box_style" value="" maxlength="1024" size="60" />
      <p><?php _e('Please enter style for this iframe box (Optional).', 'mini-iframe-box'); ?></p>
	  
      <input name="box_id" id="box_id" type="hidden" value="">
      <input type="hidden" name="box_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'mini-iframe-box'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_miniifbox_redirect()" value="<?php _e('Cancel', 'mini-iframe-box'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_miniifbox_help()" value="<?php _e('Help', 'mini-iframe-box'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('box_form_add'); ?>
    </form>
</div>
</div>