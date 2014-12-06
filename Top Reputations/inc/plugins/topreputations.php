<?php
/*
	Main plugin file for "Top Reputations" plugin for MyBB 1.8
	Copyright © 2014 Florian Berger
	Last change: 2014-10-04  11:20 PM
*/

if(!defined('IN_MYBB')) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('misc_start','topreputations');

function topreputations_info() {
	global $lang;
	
	$lang->load("topreputations");

	return array(
		"name" 			=> 'Top Reputations',
		"description" 	=> $lang->plugin_desc,
		"website"		=> 'http://forum.mybboard.de/user-9022.html',
		"author"		=> 'Florian Berger',
		"authorsite"	=> 'http://florian-berger.info',
		"version"		=> '1.1.0',
		"compatibility"	=> '18*',
		"codename"      => 'berger_florian_topreputations'
	);
}

/*
	Create template and template group when activate plugin
*/
function topreputations_activate() {
	global $db;
	
	$templateset = array(
	    "prefix" => "topreputations",
	    "title" => "Top Reputations",
    );
	$db->insert_query("templategroups", $templateset);


	// Create a template
	$templatearray = array(
        "title" => "topreputations_view",
        "template" => "<html><head><title>{\$lang->reputations_header} - {\$mybb->settings[\'bbname\']}</title>{\$headerinclude}</head><body>{\$header}<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\"><tr><td class=\"thead\"><strong>{\$lang->reputations_header}</strong></td></tr><tr><td class=\"trow1\">{\$lang->reputations_desc}<br /><br />
		<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\" style=\"width:60%;\"><tr><td class=\"thead\" style=\"width: 5%;\"><strong>{\$lang->table_header_place}</strong></td><td class=\"thead\" style=\"width: 85%;\"><strong>{\$lang->table_header_nick}</strong></td><td class=\"thead\" style=\"width: 10%;\"><strong>{\$lang->table_header_reputations}</strong></td></tr><tbody>{\$repTable}</tbody></table></td></tr></table>{\$footer}</body></html>",
				"sid" => -2
	);
	$db->insert_query("templates", $templatearray);
}

/*
	Delete template and template group when deactivate plugin
*/
function topreputations_deactivate() {
	global $db;
	
	// Delete the template
	$templatearray = array(
        "topreputations_view"
    );
	$deltemplates = implode("','", $templatearray);
	$db->delete_query("templates", "title in ('{$deltemplates}')");
	
	$db->delete_query("templategroups", "prefix in ('topreputations')");
}

function topreputations()
{
	global $mybb;
	
	if(isset($mybb->input['action']) && ($mybb->input['action'] == "reputations"))
	{
		global $db,$templates,$theme,$headerinclude,$header,$footer,$lang;
		
		$lang->load("topreputations");
		
		$repTable = "";
		$res = $db->query('SELECT uid, username, usergroup, displaygroup, reputation FROM '.TABLE_PREFIX.'users ORDER BY reputation DESC LIMIT 10');
		
		$maxplace = $res->num_rows;
		$iPlace = 1;
		
		while ($row = $db->fetch_array($res)) {
			$trow = $iPlace % 2 == 0 ? "trow1" : "trow2";
			
			$username = format_name(htmlspecialchars_uni($row['username']), intval($row['usergroup']), intval($row['displaygroup']));
			$userlink = build_profile_link($username, $row['uid'], "_blank");
			
			$user = htmlspecialchars_uni($row['username']);
			
			$repTable = $repTable . '<tr><td class="' . $trow . '" valign="top">' . $iPlace . '</td><td class="' . $trow . '" valign="top">' . $userlink . '</td><td class="' . $trow . '" valign="top">' . $row['reputation'] . '</td></tr>';
			
			$iPlace++;
		}
		
		
		
		add_breadcrumb($lang->reputations_header);
		eval("\$topreputations = \"".$templates->get("topreputations_view")."\";");
		output_page($topreputations);
		
		exit();
	}
}
?>