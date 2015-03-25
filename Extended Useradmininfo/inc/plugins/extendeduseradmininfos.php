<?php
	// Main Plugin file for the plugin Extended Useradmininfos
	// © 2013-2014 Flobo x3
	// Last change: 2014-12-07
	
if(!defined('IN_MYBB')) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('global_end', 'extendeduseradmininfos_set_info');
$plugins->add_hook('member_profile_start', 'extendeduseradmininfos_get_info');

function extendeduseradmininfos_info() {
	global $lang, $db;
	$lang->load('extendeduseradmininfo');
	
	$info = array(
		"name" 			=> $lang->extendeduseradmininfo_name,
		"description"	=> $lang->extendeduseradmininfo_desc,
		"website"		=> 'http://forum.mybboard.de/user-9022.html',
		"author"		=> 'Florian Berger',
		"authorsite"	=> 'http://florian-berger.info',
		"version"		=> '2.0.0',
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
        <table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
        <tr>
        <td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_geo_header}</strong></td>
        </tr>
        <tr>
        <td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_country}</strong></td>
        <td class=\"trow1\">___COUNTRY______COUNTRYCODE___</td>
        </tr>
        <tr>
        <td class=\"trow2\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_city}</strong></td>
        <td class=\"trow2\">___POSTALCODE______CITY___</td>
        </tr>
        <tr>
        <td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_region}</strong></td>
        <td class=\"trow1\">___REGION___</td>
        </tr>
        <tr>
        <td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_continentcode}</strong></td>
        <td class=\"trow1\">___CONTINENTCODE___</td>
        </tr>
        <tr>
        <td class=\"trow1\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_latitude}</strong></td>
        <td class=\"trow1\">___LATITUDE___</td>
        </tr>
        <tr>
        <td class=\"trow2\" width=\"40%\"><strong>{\$lang->extendeduseradmininfo_geo_longitude}</strong></td>
        <td class=\"trow2\">___LONGITUDE___</td>
        </tr>
        </table>
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
	
	$templatearray = array(
		"title" => 'extendeduseradmininfo_view_nogeoinfos',
		"template" => "
		<br />
        <table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
        <tr>
        <td colspan=\"2\" class=\"thead\"><strong>{\$lang->extendeduseradmininfo_geo_header}</strong></td>
        </tr>
        <tr>
        <td class=\"trow1\" >{\$lang->extendeduseradmininfo_no_geo_ipv6}</td>
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
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users`
			 DROP COLUMN last_useragent";
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
    global $lang, $db, $mybb, $templates, $theme, $infoTable, $advInfo, $geoTable, $geoInfo;
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
	
	$geoTemp = "";
	if (filter_var($lastip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
		// Wenn IPv4, dann Standort bestimmen
		require_once("inc/functions_extendeduseradmininfos.php");
		$geo = getGeoInformations($lastip);
		
		if ($geoTable == '') {
			eval("\$geoTable = \"".$templates->get("extendeduseradmininfo_geo_info")."\";");
		}
		
		if ($geo['Country'] == "")
            $geo['Country'] = $lang->extendeduseradmininfo_unknown;
		
		if ($geo['CountryCode'] != "")
            $geo['CountryCode'] = " (" . $geo['CountryCode'] . ")";
		
		if ($geo['City'] == "")
            $geo['City'] = $lang->extendeduseradmininfo_unknown;
		
		if ($geo['Latitude'] == "")
            $geo['Latitude'] = $lang->extendeduseradmininfo_unknown;
		
		if ($geo['Longitude'] == "")
            $geo['Longitude'] = $lang->extendeduseradmininfo_unknown;
		
		if ($geo['Region'] == "")
            $geo['Region'] = $lang->extendeduseradmininfo_unknown;
		
		if ($geo['ContinentCode'] == "")
            $geo['ContinentCode'] = $lang->extendeduseradmininfo_unknown;
		
		if ($geo['PostalCode'] != "")
			$geo['PostalCode'] .= " ";
		
		$geoTemp = str_replace(
								array('___COUNTRY___', '___COUNTRYCODE___', '___CITY___', '___LATITUDE___', '___LONGITUDE___', '___POSTALCODE___', '___REGION___', '___CONTINENTCODE___'), 
								array($geo['Country'], $geo['CountryCode'], $geo['City'], $geo['Latitude'], $geo['Longitude'], $geo['PostalCode'], $geo['Region'], $geo['ContinentCode']),
				   $geoTable);
	} else {
		if ($geoTable == '') {
			eval("\$geoTable = \"".$templates->get("extendeduseradmininfo_view_nogeoinfos")."\";");
		}
		
		$geoTemp = $geoTable;
	}
	$geoInfo = $geoTemp;
}

?>