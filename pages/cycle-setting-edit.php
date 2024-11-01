<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? $_GET['did'] : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

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
	$wpanything_errors = array();
	$wpanything_success = '';
	$wpanything_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_ANYTHING_SETTINGS."`
		WHERE `wpanything_sid` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'wpanything_sname' 		=> $data['wpanything_sname'],
		'wpanything_sdirection' => $data['wpanything_sdirection'],
		'wpanything_sspeed' 	=> $data['wpanything_sspeed'],
		'wpanything_stimeout' 	=> $data['wpanything_stimeout'],
		'wpanything_srandom' 	=> $data['wpanything_srandom'],
		'wpanything_sextra' 	=> $data['wpanything_sextra'],
		'wpanything_sid' 		=> $data['wpanything_sid']
	);
}

// Form submitted, check the data
if (isset($_POST['wpanything_form_submit']) && $_POST['wpanything_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('wpanything_form_edit');
	
	$form['wpanything_sname'] 		= isset($_POST['wpanything_sname']) ? sanitize_text_field($_POST['wpanything_sname']) : '';
	$form['wpanything_sdirection'] 	= isset($_POST['wpanything_sdirection']) ? sanitize_text_field($_POST['wpanything_sdirection']) : '';
	$form['wpanything_sspeed'] 		= isset($_POST['wpanything_sspeed']) ? sanitize_text_field($_POST['wpanything_sspeed']) : '';
	$form['wpanything_stimeout'] 	= isset($_POST['wpanything_stimeout']) ? sanitize_text_field($_POST['wpanything_stimeout']) : '';

	if($form['wpanything_sdirection'] != "scrollLeft" && $form['wpanything_sdirection'] != "scrollRight" 
		&& $form['wpanything_sdirection'] != "scrollUp" && $form['wpanything_sdirection'] != "scrollDown")
	{
		$form['wpanything_sdirection'] = "scrollLeft";
	}
	
	if(!is_numeric($form['wpanything_sspeed'])) { $form['wpanything_sspeed'] = 700; }
	if(!is_numeric($form['wpanything_stimeout'])) { $form['wpanything_stimeout'] = 5000; }
		
		
	//	No errors found, we can add this Group to the table
	if ($wpanything_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_ANYTHING_SETTINGS."`
				SET `wpanything_sdirection` = %s,
				`wpanything_sspeed` = %s,
				`wpanything_stimeout` = %s
				WHERE wpanything_sid = %d
				LIMIT 1",
				array($form['wpanything_sdirection'], $form['wpanything_sspeed'], $form['wpanything_stimeout'], $did)
			);
		$wpdb->query($sSql);
		
		$wpanything_success = __('Details was successfully updated.', 'wp-anything-slider');
	}
}

if ($wpanything_error_found == TRUE && isset($wpanything_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $wpanything_errors[0]; ?></strong></p>
	</div>
	<?php
}

if ($wpanything_error_found == FALSE && strlen($wpanything_success) > 0)
{
	?>
	<div class="updated fade">
		<p><strong><?php echo $wpanything_success; ?> 
		<a href="<?php echo WP_wpanything_ADMIN_URL; ?>&ac=showcycle"><?php _e('Click here to view the details', 'wp-anything-slider'); ?></a></strong></p>
	</div>
	<?php
}

?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Wp anything slider', 'wp-anything-slider'); ?></h2>
	<form name="wpanything_setting_form" method="post" action="#" onsubmit="return wpanything_setting_submit()"  >
      <h3><?php _e('Update details', 'wp-anything-slider'); ?></h3>
	  
	  <label for="tag-title"><?php _e('Setting name', 'wp-anything-slider'); ?></label>
		<select name="wpanything_sname" id="wpanything_sname" disabled="disabled">
			<option value="">Select</option>
            <?php
            for($i=1; $i<=10; $i++)
			{
				if($form['wpanything_sname'] == 'SETTING'.$i) 
				{ 
					$selected = "selected='selected'" ; 
				}
				else
				{
					$selected = '' ; 
				}
				echo "<option value='SETTING".$i."' $selected>SETTING".$i."</option>";
			}
			?>
          </select>
		<p><?php _e('Select a setting name.', 'wp-anything-slider'); ?></p>
				
		<label for="tag-title"><?php _e('Speed', 'wp-anything-slider'); ?></label>
		<input name="wpanything_sspeed" type="text" id="wpanything_sspeed" value="<?php echo $form['wpanything_sspeed']; ?>" maxlength="5" />
		<p><?php _e('Enter your speed.', 'wp-anything-slider'); ?> Ex: 700</p>
		
		<label for="tag-title"><?php _e('Timeout', 'wp-anything-slider'); ?></label>
		<input name="wpanything_stimeout" type="text" id="wpanything_stimeout" value="<?php echo $form['wpanything_stimeout']; ?>" maxlength="5" />
		<p><?php _e('Enter your timeout.', 'wp-anything-slider'); ?> Ex: 5000</p>
		
		<label for="tag-title"><?php _e('Direction', 'wp-anything-slider'); ?></label>
		<select name="wpanything_sdirection" id="wpanything_sdirection">
            <option value='scrollLeft' <?php if($form['wpanything_sdirection']== 'scrollLeft') { echo 'selected' ; } ?>>scrollLeft</option>
            <option value='scrollRight' <?php if($form['wpanything_sdirection'] == 'scrollRight') { echo 'selected' ; } ?>>scrollRight</option>
            <option value='scrollUp' <?php if($form['wpanything_sdirection'] == 'scrollUp') { echo 'selected' ; } ?>>scrollUp</option>
            <option value='scrollDown' <?php if($form['wpanything_sdirection'] == 'scrollDown') { echo 'selected' ; } ?>>scrollDown</option>
          </select>
		<p><?php _e('Selct cycle direction.', 'wp-anything-slider'); ?></p>
		  
      <input name="wpanything_sid" id="wpanything_sid" type="hidden" value="<?php echo $form['wpanything_sid']; ?>">
      <input type="hidden" name="wpanything_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Update Details', 'wp-anything-slider'); ?>" type="submit" />&nbsp;
        <input name="publish" lang="publish" class="button add-new-h2" onclick="wpanything_setting_redirect()" value="<?php _e('Cancel', 'wp-anything-slider'); ?>" type="button" />&nbsp;
        <input name="Help" lang="publish" class="button add-new-h2" onclick="wpanything_help()" value="<?php _e('Help', 'wp-anything-slider'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('wpanything_form_edit'); ?>
    </form>
</div>
<p class="description">
	<?php _e('Check official website for more information', 'wp-anything-slider'); ?>
	<a target="_blank" href="<?php echo Wp_wpanything_FAV; ?>"><?php _e('click here', 'wp-anything-slider'); ?></a>
</p>
</div>