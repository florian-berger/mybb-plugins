<?php
/*
 * Plugin Referers
 * © 2013-2014 Floobo x3
 * Last change: 2014-10-11
*/
	
if(!defined('IN_MYBB')) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
define('REFTABLE', 'referers');
	
$plugins->add_hook("admin_tools_menu_logs", "referers_admin_menu");
$plugins->add_hook('admin_tools_action_handler','referers_admin_action');
$plugins->add_hook('global_start', 'referers_read');
	
function referers_info() {
	global $lang;
	$lang->load('referers');
	
	return array(
		"name" 			=> $lang->referers_name,
		"description" 	=> $lang->referers_desc,
		"website"		=> 'http://forum.mybboard.de/user-9022.html',
		"author"		=> 'Florian Berger',
		"authorsite"	=> 'http://florian-berger.info',
		"version"		=> '1.2.5',
		"compatibility"	=> '16*,18*',
		"guid" 			=> '48cf5714abab0edc90d4e49554cb1636',
		"codename"      => 'berger_florian_referers'
	);
}

function referers_install() {
	global $db;
	
	$db->write_query("CREATE TABLE ".TABLE_PREFIX.REFTABLE." (
						ID int(15) NOT NULL AUTO_INCREMENT,
						UserID int (20) NOT NULL,
						UserIP VARCHAR(15) NOT NULL,
						URL VARCHAR(1000) NOT NULL,
						TS TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY (ID)
					 );");
}

function referers_is_installed() {
	$result = false;
	
	global $db;
	if ($db->table_exists(REFTABLE)) {
		$result = true;
	}
	
	return $result;
}

function referers_uninstall() {
	global $db;
	
	if ($db->table_exists(REFTABLE)) {
		$db->drop_table(REFTABLE);
	}
}

function referers_activate() {
	global $db, $lang, $mybb;
	$lang->load('referers');
		
	$insertarray = array(
		'name' => 'referers',
		'title' => $lang->settings_title,
		'description' => $lang->settings_desc,
		'disporder' => 32,
		'isdefault' => 0
	);
	$groupid = $db->insert_query("settinggroups", $insertarray);
	
	$insertarray = array(
		'name' => 'logreferersactive',
		'title' => $lang->setting_active_title,
		'description' => $lang->seting_active_desc,
		'optionscode' => 'yesno',
		'value' => 'yes',
		'disporder' => 1,
		'gid' => $groupid
	);
	$db->insert_query("settings", $insertarray);
	
	
	$bbURL = parse_url($mybb->settings['bburl']);
	$insertarray = array(
		"name" => 'referersignoredurls',
		'title' => $lang->setting_ignored_domains_title,
		'description' => $lang->setting_ignored_domains_desc,
		'optionscode' => 'text',
		'value' => $bbURL['host'],
		'disporder' => 2,
		'gid' => $groupid
	);
	$db->insert_query("settings", $insertarray);
	
	$insertarray = array(
		'name' => 'showrefererslimit',
		'title' => $lang->setting_showlimit_title,
		'description' => $lang->setting_showlimit_desc,
		'optionscode' => 'text',
		'value' => 250,
		'disporder' => 3,
		'gid' => $groupid
	);
	$db->insert_query("settings", $insertarray);
	
	rebuild_settings();
}

function referers_deactivate() {
	global $db;
	
	$db->delete_query("settings", "name IN('logreferersactive', 'showrefererslimit', 'referersignoredurls')");
	$db->delete_query("settinggroups", "name IN('referers')");
	
	rebuild_settings();
}

function referers_admin_menu(&$sub_menu) {
	global $lang;
	$lang->load("referers");
	
	$sub_menu['110'] = array
	(
		'id' => 'referers.php',
		'title' => $lang->adminmenu_referers,
		'link' => 'index.php?module=tools-referers'
	);

	return $sub_menu;
}

function referers_admin_action(&$actions) {
	$actions['referers'] = array('active' => 'referers.php', 
								 'file'   => 'referers.php');
}

function referers_read() {
	global $db, $mybb;
	
	if (($mybb->settings['logreferersactive'] == 1) || ($mybb->settings['logreferersactive'] == 'yes')) {
		$ref = $db->escape_string($_SERVER['HTTP_REFERER']);
		
		if ($ref != '') {
			$domArray = parse_url($ref);
			$RefDomain = $domArray['host'];
			
			
			$urlsIgnored = explode(',', $mybb->settings['referersignoredurls']);
			
			$allowLog = !in_array($RefDomain, $urlsIgnored);
			
			if ($allowLog) {
				$RefUserID = intval($mybb->user['uid']);
				$RefUserIP = $db->escape_string($_SERVER['REMOTE_ADDR']);
				
				$insertarray = array(
					'UserID' => $RefUserID,
					'UserIP' => $RefUserIP,
					'URL'	 => $ref
				);
				$db->insert_query(REFTABLE, $insertarray);
			}
			
			$maxReferers = $mybb->settings['showrefererslimit'];
			$sQry = "SELECT ID FROM ".TABLE_PREFIX.REFTABLE." ORDER BY ID DESC LIMIT 0,".$maxReferers;
			$tmp = $db->query($sQry);
			
			$idString;
			while($zeile = $db->fetch_array($tmp))
			{
				if ($idString != "")
					$idString .= ',';
					
				$idString .= $zeile['ID'];
			}
			
			if ($idString != '') {
				$sQry = "DELETE FROM ".TABLE_PREFIX.REFTABLE." WHERE ID NOT IN(".$idString.")";
				$db->query($sQry);
			}
		}
	}
}

?>