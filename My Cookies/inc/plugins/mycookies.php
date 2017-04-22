<?php
// Main Plugin file for the plugin My Cookies
// © 2017 Florian Berger
// ----------------------------------------
// Last Update: 2017-04-22

if(!defined('IN_MYBB')) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('misc_start','ShowCookies');

function mycookies_info() {
    global $lang;
    $lang->load('mycookies');

    return array
    (
        'name'			=> $lang->mycookies_name,
        'description'	=> $lang->mycookies_desc,
        'website'		=> 'http://community.mybb.com/user-75209.html',
        'author'		=> 'Florian Berger',
        "authorsite"	=> 'https://florian-berger.info',
        'version'		=> '1.1.0',
        'compatibility' => '18*',
        'guid'			=> 'e39882b1e67e4ab18cbdfb4c581822e3',
        'codename' 		=> 'berger_florian_cookies'
    );
}

function mycookies_activate() {
    global $db, $lang;

    $insertarray = array(
        'name' => 'mycookies',
        'title' => $db->escape_string($lang->mycookies_settings_groupname),
        'description' => $db->escape_string($lang->mycookies_settings_groupdesc),
        'disporder' => 33,
        'isdefault' => 0
    );
    $groupid = $db->insert_query("settinggroups", $insertarray);

    $insertarray = array(
        'name' => 'showcookies',
        'title' => $db->escape_string($lang->mycookies_settings_showname),
        'description' => $db->escape_string($lang->mycookies_settings_showdesc),
        'optionscode' => 'yesno',
        'value' => 'yes',
        'disporder' => 1,
        'gid' => $groupid
    );
    $db->insert_query("settings", $insertarray);

    rebuild_settings();
}

function mycookies_deactivate() {
    global $db;

    $db->delete_query("settings", "name IN('showcookies')");
    $db->delete_query("settinggroups", "name IN('referers')");

    rebuild_settings();
}

function ShowCookies() {
    global $mybb,$db,$theme,$headerinclude,$header,$footer,$lang;

    if(!isset($mybb->input['action']) || $mybb->input['action'] != "cookies") {
        // Not relevant for us - return and do nothing
        return;
    }

    $lang->load("mycookies");

    $pageTemplate = "<html><head><title>{\$lang->mycookies_page_header} - {\$mybb->settings['bbname']}</title>{\$headerinclude}</head><body>{\$header}<table border=\\\"0\\\" cellspacing=\\\"{\$theme['borderwidth']}\\\" cellpadding=\\\"{\$theme['tablespace']}\\\" class=\\\"tborder\\\"><tr><td class=\\\"thead\\\"><strong>{\$lang->mycookies_page_header}</strong></td></tr><tr><td class=\\\"trow1\\\">{\$lang->mycookies_page_desc}<br /><br />
		<table border=\\\"0\\\" cellspacing=\\\"{\$theme['borderwidth']}\\\" cellpadding=\\\"{\$theme['tablespace']}\\\" class=\\\"tborder\\\" style=\\\"width:85%;\\\"><tr><td class=\\\"thead\\\" style=\\\"width: 20%;\\\"><strong>{\$lang->table_header_name}</strong></td><td class=\\\"thead\\\" style=\\\"width: 80%;\\\"><strong>{\$lang->table_header_value}</strong></td></tr><tbody>{\$cookiesTable}</tbody></table>
		</td></tr></table>{\$footer}</body></html>";

    $cookiesTable = '';
    if (($mybb->settings['showcookies'] == 1) || ($mybb->settings['showcookies'] == 'yes')) {
        $i = 0;
        foreach ($_COOKIE as $cName=>$cValue)
        {
            $i++;
            $trow = $i % 2 == 0 ? "trow2" : "trow1";

            $cookiesTable .= '<tr><td class="'.$trow.'" valign="top">'.$cName.'</td><td class="'.$trow.'" valign="top">'.$cValue.'</td></tr>';
            //echo $cName.' is '.$cValue."<br>\n";
        }
    } else {
        $cookiesTable .= '<tr><td class="trow1" valign="top" colspan="2">'.$lang->table_cookies_disabled.'</td></tr>';
    }

    add_breadcrumb($lang->mycookies_page_header);

    $cookies = "";
    eval("\$cookies = \"".$pageTemplate."\";");
    output_page($cookies);
}