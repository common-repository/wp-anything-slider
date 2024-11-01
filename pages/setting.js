function wpanything_setting_redirect()
{
	window.location = "options-general.php?page=wp-anything-slider&ac=showcycle";
}

function wpanything_help()
{
	window.open("http://www.gopiplus.com/work/2012/04/20/wordpress-plugin-wp-anything-slider/");
}

function wpanything_setting_submit()
{
	if(document.wpanything_setting_form.wpanything_sspeed.value=="" || isNaN(document.wpanything_setting_form.wpanything_sspeed.value))
	{
		alert(wp_anything_adminscripts.wpanything_sspeed);
		document.wpanything_setting_form.wpanything_sspeed.focus();
		return false;
	}
	else if(document.wpanything_setting_form.wpanything_stimeout.value=="" || isNaN(document.wpanything_setting_form.wpanything_stimeout.value))
	{
		alert(wp_anything_adminscripts.wpanything_stimeout);
		document.wpanything_setting_form.wpanything_stimeout.focus();
		return false;
	}
	else if(document.wpanything_setting_form.wpanything_sdirection.value=="")
	{
		alert(wp_anything_adminscripts.wpanything_sdirection);
		document.wpanything_setting_form.wpanything_sdirection.focus();
		return false;
	}
}

function wpanything_content_delete(id)
{
	if(confirm(wp_anything_adminscripts.wpanything_delete))
	{
		document.frm_wpanything_display.action="options-general.php?page=wp-anything-slider&ac=del&did="+id;
		document.frm_wpanything_display.submit();
	}
}	

function wpanything_content_redirect()
{
	window.location = "options-general.php?page=wp-anything-slider";
}

function wpanything_content_submit()
{
	if(document.wpanything_content_form.content.value=="")
	{
		alert(wp_anything_adminscripts.wpanything_content);
		document.wpanything_content_form.content.focus();
		return false;
	}
	else if(document.wpanything_content_form.wpanything_csetting.value=="")
	{
		alert(wp_anything_adminscripts.wpanything_csetting);
		document.wpanything_content_form.wpanything_csetting.focus();
		return false;
	}
}