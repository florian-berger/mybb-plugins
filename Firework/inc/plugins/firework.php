<?php
// Main Plugin file for the plugin Firework
// © 2013-2017 Flobo x3
// ----------------------------------------
// Last Update: 2017-04-13

if(!defined('IN_MYBB')) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}	

$plugins->add_hook("usercp_options_end", "firework_usercp");
$plugins->add_hook("usercp_do_options_end", "firework_usercp");

$plugins->add_hook('pre_output_page', 'firework');

function firework_info()
{
	global $lang;
	$lang->load('fireworks');

	return array
	(
		'name'			=> $lang->firework_info_name,
		'description'	=> $lang->firework_info_desc,
		'website'		=> 'http://community.mybb.com/user-75209.html',
		'author'		=> 'Flobo x3',
		'authorsite'	=> 'http://forum.mybboard.de/user-9022.html',
		'version'		=> '1.3.0',
		'compatibility' => '14*,16*,18*',
		'guid'			=> '123fab94fb6b3d7a6ebaf589f1c8291f',
		'codename' 		=> 'berger_florian_firework'
	);
}

function firework_install() {
	global $db, $mybb, $lang;
	
	// Add field for user option
	$db->query("ALTER TABLE ".TABLE_PREFIX."users ADD showFirework int NOT NULL default '1'");
}

function firework_is_installed()
{
	global $db;
	
	if($db->field_exists("showFirework", "users"))
		return true;
	
	return false;
}

function firework_uninstall()
{
	global $db;
	
	if($db->field_exists("showFirework", "users"))
		$db->query("ALTER TABLE ".TABLE_PREFIX."users DROP COLUMN showFirework");
}

function firework_usercp() {
	global $db, $mybb, $templates, $user, $lang;
	$lang->load('fireworks');
	
	if($mybb->request_method == "post")
	{
		$update_array = array(
			"showFirework" => intval($mybb->input['showFirework'])
		);
		
		$db->update_query("users", $update_array, "uid = '".$user['uid']."'");
	}
	
	$add_option = '</tr><tr>
<td valign="top" width="1"><input type="checkbox" class="checkbox" name="showFirework" id="showFirework" value="1" {$GLOBALS[\'$showFireworksChecked\']} /></td>
<td><span class="smalltext"><label for="showFirework">{$lang->firework_show_question}</label></span></td>';

	$find = '{$lang->show_codebuttons}</label></span></td>';
	$templates->cache['usercp_options'] = str_replace($find, $find.$add_option, $templates->cache['usercp_options']);
	
	$GLOBALS['$showFireworksChecked'] = '';
	if($user['showFirework'])
		$GLOBALS['$showFireworksChecked'] = "checked=\"checked\"";
}

function firework($site)
{
	global $mybb;
	if ($mybb->user['showFirework'])
		$site = str_replace('</head>','<script type="text/javascript" src="'.$mybb->settings['bburl'].'/jscripts/firework.js"></script></head>',$site);
	return $site;
}

?>