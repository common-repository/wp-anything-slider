<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_wpanything_display']) && $_POST['frm_wpanything_display'] == 'yes')
{
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$wpanything_success = '';
	$wpanything_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$sSql = $wpdb->prepare(
		"SELECT COUNT(*) AS `count` FROM ".WP_ANYTHING_SETTINGS."
		WHERE `wpanything_sid` = %d",
		array($did)
	);

	$result = '0';
	$result = $wpdb->get_var($sSql);
	
	if ($result != '1')
	{
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', 'wp-anything-slider'); ?></strong></p></div><?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('wpanything_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_ANYTHING_SETTINGS."`
					WHERE `wpanything_sid` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$wpanything_success_msg = TRUE;
			$wpanything_success = __('Selected record was successfully deleted ('.$did.').', 'wp-anything-slider');
		}
	}
	
	if ($wpanything_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $wpanything_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php _e('Wp anything slider', 'wp-anything-slider'); ?></h2>
    <h3><?php _e('Setting Management', 'wp-anything-slider'); ?>
	<!--<a class="add-new-h2" href="<?php //echo WP_wpanything_ADMIN_URL; ?>&amp;ac=addcycle">Add New</a>--></h3>
	<div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_ANYTHING_SETTINGS."` order by wpanything_sid asc";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<form name="frm_wpanything_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Setting Name', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Short Code', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Speed', 'wp-anything-slider'); ?></th>
            <th scope="col"><?php _e('Direction', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Timeout', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Action', 'wp-anything-slider'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Setting Name', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Short Code', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Speed', 'wp-anything-slider'); ?></th>
            <th scope="col"><?php _e('Direction', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Timeout', 'wp-anything-slider'); ?></th>
			<th scope="col"><?php _e('Action', 'wp-anything-slider'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			if(count($myData) > 0 )
			{
				foreach ($myData as $data)
				{
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td><?php echo esc_html($data['wpanything_sname']); ?></td>
						<td>[wp-anything-slider setting="<?php echo esc_html($data['wpanything_sname']); ?>"]</td>
						<td><?php echo esc_html($data['wpanything_sspeed']); ?></td>
						<td><?php echo esc_html($data['wpanything_sdirection']); ?></td>
						<td><?php echo esc_html($data['wpanything_stimeout']); ?></td>
						<td>
						<a title="Edit" href="<?php echo WP_wpanything_ADMIN_URL; ?>&amp;ac=editcycle&amp;did=<?php echo $data['wpanything_sid']; ?>"><?php _e('Edit', 'wp-anything-slider'); ?></a>
						<!--<span class="trash"><a onClick="javascript:wpanything_content_delete('<?php //echo $data['wpanything_sid']; ?>')" href="javascript:void(0);">Delete</a></span> -->
						</td>
					</tr>
					<?php 
					$i = $i+1; 
				} 	
			}
			else
			{
				?><tr><td colspan="6" align="center"><?php _e('No records available.', 'wp-anything-slider'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('wpanything_form_show'); ?>
		<input type="hidden" name="frm_wpanything_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <!--<a href="<?php //echo WP_wpanything_ADMIN_URL; ?>&amp;ac=add"><input class="button action" type="button" value="<?php //_e('Add New', 'wp-anything-slider'); ?>" /></a>-->
	  <a href="<?php echo WP_wpanything_ADMIN_URL; ?>&amp;ac=show"><input class="button action" type="button" value="<?php _e('Text Management', 'wp-anything-slider'); ?>" /></a>
	  <a href="<?php echo WP_wpanything_ADMIN_URL; ?>&amp;ac=showcycle"><input class="button action" type="button" value="<?php _e('Setting', 'wp-anything-slider'); ?>" /></a>
	  <a target="_blank" href="<?php echo Wp_wpanything_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help', 'wp-anything-slider'); ?>" /></a>
	  <a target="_blank" href="<?php echo Wp_wpanything_FAV; ?>"><input class="button button-primary" type="button" value="<?php _e('Live Demo', 'wp-anything-slider'); ?>" /></a>
	  </div>
	</div>
</div>