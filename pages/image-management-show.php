<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if (isset($_POST['frm_box_display']) && sanitize_text_field($_POST['frm_box_display']) == 'yes') {
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	if(!is_numeric($did)) { 
		die('<p>Are you sure you want to do this?</p>'); 
	}
	
	$success_msg = false;
	$result = miniifbox_cls_dbquery::miniifbox_count($did);

	if ($result != '1') {
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'mini-iframe-box'); ?></strong></p></div><?php
	}
	else {
		if (isset($_GET['ac']) && sanitize_text_field($_GET['ac']) == 'del' && isset($_GET['did']) && sanitize_text_field($_GET['did']) != '') {
			check_admin_referer('box_form_show');
			miniifbox_cls_dbquery::miniifbox_delete($did);
			$success_msg = true;
		}
	}
	
	if ($success_msg == true) {
		?><div class="updated fade"><p><strong><?php _e('Selected record was successfully deleted.', 'mini-iframe-box'); ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
    <h2><?php _e('Mini iframe box', 'mini-iframe-box'); ?>
	<a class="add-new-h2" href="<?php echo MINIIFBOX_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'mini-iframe-box'); ?></a></h2>
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = miniifbox_cls_dbquery::miniifbox_select();
	?>
	<form name="frm_box_display" method="post">
      <table class="widefat" cellspacing="0">
        <thead>
          <tr>
		  	<th><?php _e('ID', 'mini-iframe-box'); ?></th>
			<th><?php _e('Title', 'mini-iframe-box'); ?></th>
			<th><?php _e('Group', 'mini-iframe-box'); ?></th>
			<th><?php _e('Width (%)', 'mini-iframe-box'); ?></th>
			<th><?php _e('Height (px)', 'mini-iframe-box'); ?></th>
			<th><?php _e('Status', 'mini-iframe-box'); ?></th>
			<th><?php _e('Date', 'mini-iframe-box'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th><?php _e('ID', 'mini-iframe-box'); ?></th>
			<th><?php _e('Title', 'mini-iframe-box'); ?></th>
			<th><?php _e('Group', 'mini-iframe-box'); ?></th>
			<th><?php _e('Width (%)', 'mini-iframe-box'); ?></th>
			<th><?php _e('Height (px)', 'mini-iframe-box'); ?></th>
			<th><?php _e('Status', 'mini-iframe-box'); ?></th>
			<th><?php _e('Date', 'mini-iframe-box'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 ) {
			foreach ($myData as $data) {
				?>
				<tr class="<?php if ($i&1) { echo ''; } else { echo 'alternate'; }?>">
					<td><?php echo $data['box_id']; ?></td>
					<td>
					<?php echo $data['box_title']; ?>
					<div class="row-actions">
					<span class="edit">
						<a title="Edit" href="<?php echo MINIIFBOX_ADMIN_URL; ?>&ac=edit&amp;did=<?php echo $data['box_id']; ?>">
							<?php esc_html_e('Edit', 'mini-iframe-box'); ?>
						</a> | 
					</span>
					<span class="trash">
						<a onClick="javascript:_miniifbox_delete('<?php echo $data['box_id']; ?>')" href="javascript:void(0);">
							<?php esc_html_e('Delete', 'mini-iframe-box'); ?>
						</a>
					</span> 
					</div>
					</td>
					<td><?php echo $data['box_group']; ?></td>
					<td><?php echo $data['box_width']; ?></td>
					<td><?php echo $data['box_height']; ?></td>
					<td><?php echo miniifbox_cls_dbquery::miniifbox_common_text($data['box_status']); ?></td>
					<td>
					<?php
					$box_start = $data['box_start'];
					$box_end = $data['box_end'];
					$now_strtotime = strtotime(date("Y-m-d"));
					$str_strtotime = strtotime($data['box_start']);
					$end_strtotime = strtotime($data['box_end']);
					if($end_strtotime < $now_strtotime) {
						$box_end = '<span style="color:#FF0000;">' . $data['box_end'] . '</span>';
					}
					if($str_strtotime > $now_strtotime) {
						$box_start = '<span style="color:#FF0000;">' . $data['box_start'] . '</span>';
					}
					?>
					<?php _e('Start', 'mini-iframe-box'); ?> : <?php echo $box_start; ?> <br />
					<?php _e('End', 'mini-iframe-box'); ?> : <?php echo $box_end; ?>
					</td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else {
			?><tr><td colspan="7" align="center"><?php _e('No records available', 'mini-iframe-box'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('box_form_show'); ?>
		<input type="hidden" name="frm_box_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo MINIIFBOX_ADMIN_URL; ?>&amp;ac=add">
	  <input class="button button-primary" type="button" value="<?php _e('Add New', 'mini-iframe-box'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2020/04/12/mini-iframe-box-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Short Code', 'mini-iframe-box'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2020/04/12/mini-iframe-box-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Help', 'mini-iframe-box'); ?>" /></a>
	  </div>
	</div>
</div>