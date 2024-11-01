<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? $_GET['did'] : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

// First check if ID exist with requested ID
$sSql = $wpdb->prepare(
	"SELECT COUNT(*) AS `count` FROM ".WP_ANYTHING_CONTENT."
	WHERE `wpanything_cid` = %d",
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
		FROM `".WP_ANYTHING_CONTENT."`
		WHERE `wpanything_cid` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'wpanything_ctitle' => $data['wpanything_ctitle'],
		'wpanything_cstartdate' => $data['wpanything_cstartdate'],
		'wpanything_cenddate' => $data['wpanything_cenddate'],
		'wpanything_csetting' => $data['wpanything_csetting'],
		'wpanything_cid' => $data['wpanything_cid']
	);
}
// Form submitted, check the data
if (isset($_POST['wpanything_form_submit']) && $_POST['wpanything_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('wpanything_form_edit');
	
	$form['wpanything_ctitle'] = isset($_POST['content']) ? wp_filter_post_kses($_POST['content']) : '';
	if ($form['wpanything_ctitle'] == '')
	{
		$wpanything_errors[] = __('Please enter the text/announcement.', 'wp-anything-slider');
		$wpanything_error_found = TRUE;
	}
	$form['wpanything_csetting'] = isset($_POST['wpanything_csetting']) ? sanitize_text_field($_POST['wpanything_csetting']) : '';
	if ($form['wpanything_csetting'] == '')
	{
		$wpanything_errors[] = __('Please select the setting name.', 'wp-anything-slider');
		$wpanything_error_found = TRUE;
	}
	//	No errors found, we can add this Group to the table
	if ($wpanything_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_ANYTHING_CONTENT."`
				SET `wpanything_ctitle` = %s,
				`wpanything_csetting` = %s
				WHERE wpanything_cid = %d
				LIMIT 1",
				array($form['wpanything_ctitle'], $form['wpanything_csetting'], $did)
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
		<a href="<?php echo WP_wpanything_ADMIN_URL; ?>"><?php _e('Click here to view the details', 'wp-anything-slider'); ?></a></strong></p>
	</div>
	<?php
}

add_filter('admin_head','ShowTinyMCE');
function ShowTinyMCE() 
{
	// conditions here
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'jquery-color' );
	wp_print_scripts('editor');
	if (function_exists('add_thickbox')) add_thickbox();
	wp_print_scripts('media-upload');
	if (function_exists('wp_tiny_mce')) wp_tiny_mce();
	wp_admin_css();
	wp_enqueue_script('utils');
	do_action("admin_print_styles-post-php");
	do_action('admin_print_styles');
}
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Wp anything slider', 'wp-anything-slider'); ?></h2>
	<form name="wpanything_content_form" method="post" action="#" onsubmit="return wpanything_content_submit()"  >
      <h3><?php _e('Update details', 'wp-anything-slider'); ?></h3>
	  
	  	<label for="tag-title"><?php _e('Text/Announcement', 'wp-anything-slider'); ?></label>
		<?php wp_editor(stripslashes($form['wpanything_ctitle']), "content");?>
		<p><?php _e('Enter your slider content.', 'wp-anything-slider'); ?></p>
		
		<label for="tag-title"><?php _e('Setting', 'wp-anything-slider'); ?></label>
		<select name="wpanything_csetting" id="wpanything_csetting">
			<option value="">Select</option>
			<?php
			for($i=1; $i<=10; $i++)
			{
				if($form['wpanything_csetting'] == 'SETTING'.$i) 
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
		<p><?php _e('Enter your setting name.', 'wp-anything-slider'); ?></p>

      <input name="wpanything_cid" id="wpanything_cid" type="hidden" value="<?php echo $form['wpanything_cid']; ?>">
      <input type="hidden" name="wpanything_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Update Details', 'wp-anything-slider'); ?>" type="submit" />&nbsp;
        <input name="publish" lang="publish" class="button add-new-h2" onclick="wpanything_content_redirect()" value="<?php _e('Cancel', 'wp-anything-slider'); ?>" type="button" />&nbsp;
        <input name="Help" lang="publish" class="button add-new-h2" onclick="wpanything_help()" value="<?php _e('Help', 'wp-anything-slider'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('wpanything_form_edit'); ?>
    </form>
</div>
<p class="description">
	<?php _e('Check official website for more information and live demo', 'wp-anything-slider'); ?>
	<a target="_blank" href="<?php echo Wp_wpanything_FAV; ?>"><?php _e('click here', 'wp-anything-slider'); ?></a>
</p>
</div>