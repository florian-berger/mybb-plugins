<?php
/*
 * Plugin Referers
 * © 2013-2014 Floobo x3
 * Last change: 2015-01-06
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
		"version"		=> '1.3.0',
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
						BinaryUserIP VARBINARY(15) NOT NULL,
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
		'title' => $db->escape_string($lang->settings_title),
		'description' => $db->escape_string($lang->settings_desc),
		'disporder' => 32,
		'isdefault' => 0
	);
	$groupid = $db->insert_query("settinggroups", $insertarray);
	
	$insertarray = array(
		'name' => 'logreferersactive',
		'title' => $db->escape_string($lang->setting_active_title),
		'description' => $db->escape_string($lang->seting_active_desc),
		'optionscode' => 'yesno',
		'value' => 'yes',
		'disporder' => 1,
		'gid' => $groupid
	);
	$db->insert_query("settings", $insertarray);
	
	
	$bbURL = parse_url($mybb->settings['bburl']);
	$insertarray = array(
		"name" => 'referersignoredurls',
		'title' => $db->escape_string($lang->setting_ignored_domains_title),
		'description' => $db->escape_string($lang->setting_ignored_domains_desc),
		'optionscode' => 'text',
		'value' => $bbURL['host'],
		'disporder' => 2,
		'gid' => $groupid
	);
	$db->insert_query("settings", $insertarray);
	
	$insertarray = array(
		'name' => 'showrefererslimit',
		'title' => $db->escape_string($lang->setting_showlimit_title),
		'description' => $db->escape_string($lang->setting_showlimit_desc),
		'optionscode' => 'text',
		'value' => 250,
		'disporder' => 3,
		'gid' => $groupid
	);
	$db->insert_query("settings", $insertarray);
	
	rebuild_settings();
	
	
	// update database: remove static ip field and add binary field
	$result = $db->query("SHOW COLUMNS FROM `" . TABLE_PREFIX . REFTABLE . "` LIKE 'UserIP'");
	if ($result->num_rows > 0) {
		$checkIfColumnExistQuery = $db->query("SHOW COLUMNS FROM `" . TABLE_PREFIX . REFTABLE . "` LIKE 'BinaryUserIP'");
		if ($checkIfColumnExistQuery->num_rows < 1)
			$db->query("ALTER TABLE `" . TABLE_PREFIX . REFTABLE . "` ADD BinaryUserIP VARBINARY(15) NOT NULL AFTER UserIP");
		
		$allRefs = $db->query("SELECT ID, UserIP FROM `" . TABLE_PREFIX . REFTABLE . "`");
		while ($item = $db->fetch_array($allRefs)) {
			$binaryip = $db->escape_binary(my_inet_pton($item['UserIP']));
			
			$mybb->binary_fields[REFTABLE] = array('BinaryUserIP' => true);
			$db->query("UPDATE `" . TABLE_PREFIX . REFTABLE . "` SET BinaryUserIP=" . $binaryip . " WHERE ID=" . $item['ID']);
		}
		
		$db->query("ALTER TABLE `" . TABLE_PREFIX . REFTABLE . "` DROP COLUMN UserIP");
	}
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
	global $db, $mybb, $session;
	
	if (($mybb->settings['logreferersactive'] == 1) || ($mybb->settings['logreferersactive'] == 'yes')) {
		$ref = $db->escape_string($_SERVER['HTTP_REFERER']);
		
		if ($ref != '') {
			$domArray = parse_url($ref);
			$RefDomain = $domArray['host'];
			
			$urlsIgnored = explode(',', $mybb->settings['referersignoredurls']);
			
			$allowLog = !in_array($RefDomain, $urlsIgnored);
			
			if ($allowLog) {
				$mybb->binary_fields[REFTABLE] = array('BinaryUserIP' => true);
				
				$RefUserID = intval($mybb->user['uid']);
				$RefUserIP = $db->escape_binary($session->packedip);
				
				$insertarray = array(
					'UserID' 		=> $RefUserID,
					'BinaryUserIP' 	=> $RefUserIP,
					'URL'	 		=> $ref
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