<?php
	// Main Plugin file for the plugin Extended Useradmininfos
	// © 2013-2019 Florian Berger
	// Last change: 2019-11-02
	
if(!defined('IN_MYBB')) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('pre_output_page', 'add_script_to_head');
$plugins->add_hook('global_end', 'extendeduseradmininfos_set_info');
$plugins->add_hook('member_profile_start', 'extendeduseradmininfos_get_info');

function extendeduseradmininfos_info() {
	global $lang, $db;
	$lang->load('extendeduseradmininfo');

    $desc = $lang->extendeduseradmininfo_desc . '<br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="X9ULXRYHP84ZY">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>';
	
	$info = array(
		"name" 			=> $lang->extendeduseradmininfo_name,
		"description"	=> $desc,
		'website'		=> 'http://community.mybb.com/user-75209.html',
		'author'		=> 'Florian Berger',
		"authorsite"	=> 'https://berger-media.biz',
		"version"		=> '3.0.0',
		"compatibility" => '16*,18*',
		"guid" 			=> '138867d0b45740bce59f3e48dc72c893',
		"codename"		=> 'berger_florian_useradmininfo'
	);
	
	// show hint if last_ip field still exists
	$result = $db->query("SHOW COLUMNS FROM `" . TABLE_PREFIX . "users` LIKE 'last_ip'");
	if ($result->num_rows > 0)
		$info["description"] .= "<br /><br />" . $lang->extendeduseradmininfo_dbchanged;
	
	return $info;
}

function extendeduseradmininfos_activate() {
	global $db;
	
	$templateset = array(
	    "prefix" => "extendeduseradmininfo",
	    "title" => "Extended Useradmininfo",
    );
	$db->insert_query("templategroups", $templateset);


	// Create a template
	$templatearray = array(
        "title" => "extendeduseradmininfo_view",
        "template" => "
		<br />
        <table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
        <tr>
        <td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_tableheader}</strong></td>
        </tr>
        <tr>
        <td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_ip}</strong></td>
        <td class=\"trow1\">___LASTIP___</td>
        </tr>
        <tr>
        <td class=\"trow2\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_useragent}</strong></td>
        <td class=\"trow2\">___AGENT___</td>
        </tr>
        <tr>
        <td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_browser}</strong></td>
        <td class=\"trow1\">___BROWSER___</td>
        </tr>
        <tr>
        <td class=\"trow2\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_operatingsystem}</strong></td>
        <td class=\"trow2\">___OS___</td>
        </tr>
        </table>
		",
		"sid" => -2
	);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
        "title" => "extendeduseradmininfo_geo_info",
        "template" => "
<br />
<div id=\"geo_info_loading\">
	<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
		<tr>
			<td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_geo_header}</strong></td>
		</tr>
		<tr>
			<td colspan=\"2\">
				{\$lang->extendeduseradmininfo_geo_loading}
				<div id=\"last_user_ip_address\" class=\"hidden\">{\$lastip}</div>
			</td>
		</tr>
	</table>
</div>

<div id=\"geo_info_data\" class=\"hidden\">
	<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
		<tr>
			<td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_geo_header}</strong></td>
		</tr>
		<tr>
			<td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_country}</strong></td>
			<td class=\"trow1\" id=\"geo_country\"></td>
		</tr>
		<tr>
			<td class=\"trow2\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_city}</strong></td>
			<td class=\"trow2\" id=\"geo_city\"></td>
		</tr>
		<tr>
			<td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_region}</strong></td>
			<td class=\"trow1\" id=\"geo_region\"></td>
		</tr>
		<tr>
			<td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_latitude}</strong></td>
			<td class=\"trow1\" id=\"geo_lat\"></td>
		</tr>
		<tr>
			<td class=\"trow2\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_longitude}</strong></td>
			<td class=\"trow2\" id=\"geo_long\"></td>
		</tr>
	</table>
</div>

<div id=\"geo_info_error\" class=\"hidden\">
	<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
		<tr>
			<td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_geo_header}</strong></td>
		</tr>
		<tr>
			<td colspan=\"2\">{\$lang->extendeduseradmininfo_geo_error_loading}</td>
		</tr>
	</table>
</div>

<script type=\"text/javascript\">
	loadGeoInformation();
</script>
		",
		"sid" => -2
	);
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		"title" => 'extendeduseradmininfo_view_noinfos',
		"template" => "
		<br />
        <table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
        <tr>
        <td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_tableheader}</strong></td>
        </tr>
        <tr>
        <td class=\"trow1\" >{\$lang->extendeduseradmininfo_no_informations_saved}</td>
        </tr>
        </table>
		",
		"sid" => -2
	);
	$db->insert_query("templates",$templatearray);
	
	// Edit AdministratorOptions Template
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets('member_profile_adminoptions', '#</table>#', '</table>{$advInfo}{$geoInfo}');
}

function extendeduseradmininfos_deactivate() {
	global $db;
	
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets('member_profile_adminoptions', '#\{\$advInfo\}#', '', 0);
	find_replace_templatesets('member_profile_adminoptions', '#\{\$geoInfo\}#', '', 0);
	
	// refresh database structure -> remove plugin added field for last ip and use mybb own field
	$result = $db->query("SHOW COLUMNS FROM `" . TABLE_PREFIX . "users` LIKE 'last_ip'");
	if ($result->num_rows > 0)
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "users` DROP COLUMN last_ip");
	
	// Delete the template
	$templatearray = array(
        "extendeduseradmininfo_view",
		"extendeduseradmininfo_view_noinfos",
		"extendeduseradmininfo_geo_info",
		"extendeduseradmininfo_view_nogeoinfos"
    );
	$deltemplates = implode("','", $templatearray);
	
	$db->delete_query("templates", "title in ('{$deltemplates}')");
	$db->delete_query("templategroups", "prefix IN('extendeduseradmininfo')");
}

function extendeduseradmininfos_install() {
	global $db;
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users` ADD last_useragent VARCHAR(255)";
	$db->query($sQry);
}

function extendeduseradmininfos_is_installed() {
	global $db;
        
	if($db->field_exists("last_useragent", "users"))
		return true;
			
	return false; 
}

function extendeduseradmininfos_uninstall() {
	global $db;
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users` DROP COLUMN last_useragent";
	$db->write_query($sQry);
}

function extendeduseradmininfos_set_info() {
	global $db, $mybb;
	$uid = $mybb->user['uid'];

	if ($uid > 0) {
		$useragent =  $db->escape_string($_SERVER['HTTP_USER_AGENT']);
		$ip = $db->escape_string($_SERVER['REMOTE_ADDR']);
		
		$sQry = "UPDATE " . TABLE_PREFIX . "users SET last_useragent='$useragent' WHERE uid=" . $uid;
		$db->write_query($sQry);
	}
}

function extendeduseradmininfos_get_info() {
    global $lang, $db, $mybb, $templates, $theme, $infoTable, $advInfo, $geoInfo;
    $lang->load('extendeduseradmininfo');
	
	$userid = intval($mybb->input['uid']);
	
	$query = $db->simple_select("users", "*", "uid='{$userid}'");
	$infomember = $db->fetch_array($query);
	
    $lastip = my_inet_ntop($db->unescape_binary($infomember['lastip']));
    $lastagent = $infomember['last_useragent'];
    if ($lastagent != "") {
		require_once("inc/functions_extendeduseradmininfos.php");
        $browser = getBrowser($lastagent);
        
		if ($infoTable == '') {
			eval("\$infoTable = \"".$templates->get("extendeduseradmininfo_view")."\";");
		}
    
        if ($lastip != "") {
            $ipadress  = $lastip;
        } else {
            $ipadress = $lang->extendeduseradmininfo_unknown;
        }
        
        if ($lastagent != "") {
            $useragent = htmlspecialchars($lastagent, ENT_QUOTES);
        } else {
            $useragent = $lang->extendeduseradmininfo_unknown;
        }
        
        if ($browser['browser'] != "") {
            $browsername = $browser['browser'] . " " . $browser['version'];
        } else {
            $browsername = $lang->extendeduseradmininfo_unknown;
        }
        
        if ($browser['platform'] != "") {
            $operatingsys = $browser['platform'];
        } else {
            $operatingsys = $lang->extendeduseradmininfo_unknown;
        }
		
        $temp = str_replace(array('___LASTIP___', '___AGENT___', '___BROWSER___', '___OS___'), array($ipadress, $useragent, $browsername, $operatingsys), $infoTable);
    } else {
		if ($infoTable == '') {
			eval("\$infoTable = \"".$templates->get("extendeduseradmininfo_view_noinfos")."\";");
		}
		
		$temp = $infoTable;
	}
    $advInfo = $temp;

    eval("\$geoInfo = \"".$templates->get("extendeduseradmininfo_geo_info")."\";");
}

function add_script_to_head($site) {
    global $mybb;

    $site = str_replace('</head>','<script type="text/javascript" src="'.$mybb->settings['bburl'].'/jscripts/extendeduseradmininfo.js"></script></head>',$site);

    return $site;
}

?>