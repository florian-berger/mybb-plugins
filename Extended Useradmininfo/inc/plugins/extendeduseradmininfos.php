<?php
	// Main Plugin file for the plugin Extended Useradmininfos
	// © 2013-2014 Flobo x3
	// Last change: 2014-11-22
	
if(!defined('IN_MYBB')) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('global_end', 'extendeduseradmininfos_set_info');
$plugins->add_hook('member_profile_start', 'extendeduseradmininfos_get_info');

function extendeduseradmininfos_info() {
	global $lang;
	$lang->load('extendeduseradmininfo');
	
	return array(
		"name" 			=> $lang->extendeduseradmininfo_name,
		"description"	=> $lang->extendeduseradmininfo_desc,
		"website"		=> 'http://forum.mybboard.de/user-9022.html',
		"author"		=> 'Florian Berger',
		"authorsite"	=> 'http://florian-berger.info',
		"version"		=> '1.4.0',
		"compatibility" => '16*,18*',
		"guid" 			=> '138867d0b45740bce59f3e48dc72c893',
		"codename"		=> 'berger_florian_useradmininfo'
	);
}

function extendeduseradmininfos_activate() {
	
}

function extendeduseradmininfos_deactivate() {
	
}

function extendeduseradmininfos_install() {
	global $db;
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users` ADD last_ip VARCHAR(15)";
	$db->query($sQry);
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users` ADD last_useragent VARCHAR(255)";
	$db->query($sQry);
	
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
	find_replace_templatesets('member_profile_adminoptions', '#</table>#', '</table>{$advInfo}');
}

function extendeduseradmininfos_is_installed() {
	global $db;
	
	$result = $db->query("SHOW COLUMNS FROM `" . TABLE_PREFIX . "users` LIKE 'last_useragent'");
	
	if ($result->num_rows > 0)
		return true;
	
	return false;
}

function extendeduseradmininfos_uninstall() {
	global $db;
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users`
			 DROP COLUMN last_ip";
	$db->write_query($sQry);
	
	$sQry = "ALTER TABLE `" . TABLE_PREFIX . "users`
			 DROP COLUMN last_useragent";
	$db->write_query($sQry);
	
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets('member_profile_adminoptions', '#\{\$advInfo\}#', '', 0);
	
	// Delete the template
	$templatearray = array(
        "extendeduseradmininfo_view",
		"extendeduseradmininfo_view_noinfos"
    );
	$deltemplates = implode("','", $templatearray);
	$db->delete_query("templates", "title in ('{$deltemplates}')");
}

function extendeduseradmininfos_set_info() {
	global $db, $mybb;
	$uid = $mybb->user['uid'];

	if ($uid > 0) {
		$useragent =  $db->escape_string($_SERVER['HTTP_USER_AGENT']);
		$ip = $db->escape_string($_SERVER['REMOTE_ADDR']);
		
		$sQry = "UPDATE " . TABLE_PREFIX . "users SET last_ip='$ip', last_useragent='$useragent' WHERE uid=" . $uid;
		$db->write_query($sQry);
	}
}

function getBrowser($u_agent)
{
    $bname = '';
    $platform = '';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        if (preg_match('/NT 5.0/i', $u_agent)) {
			$platform = 'Windows 2000';
		} elseif (preg_match('/NT 5.1/i', $u_agent)) {
			$platform = 'Windows XP';
		} elseif (preg_match('/NT 6.0/i', $u_agent)) {
			$platform = 'Windows Vista';
		} elseif (preg_match('/NT 6.1/i', $u_agent)) {
			$platform = 'Windows 7';
		} elseif (preg_match('/NT 6.2/i', $u_agent)) {
			$platform = 'Windows 8';
		} elseif (preg_match('/NT 6.3/i', $u_agent)) {
			$platform = 'Windows 8.1';
		} elseif (preg_match('/NT 6.4/i', $u_agent)) {
			$platform = 'Windows 10';
		} else {
			$platform = 'Windows';
		}
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if(!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)){
            $version = $matches['version'][0];
        }
        else {
            $version = $matches['version'][1];
        }
    }
    else {
        $version = $matches['version'][0];
    }
   
    // check if we have a number
    if ($version == null || $version == "") {$version = "?";}
   
    return array(
        'userAgent' => $u_agent,
        'browser'   => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 

function extendeduseradmininfos_get_info() {
    global $lang, $db, $mybb, $templates, $theme, $infoTable, $advInfo;
    $lang->load('extendeduseradmininfo');
	
	$userid = intval($mybb->input['uid']);
	
	$query = $db->simple_select("users", "*", "uid='{$userid}'");
	$infomember = $db->fetch_array($query);
	
    $lastip = $infomember['last_ip'];
    $lastagent = $infomember['last_useragent'];
    if ($lastagent != "") {
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
}


?>