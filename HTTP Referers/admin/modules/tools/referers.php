<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
define('REFTABLE', 'referers');

$page->add_breadcrumb_item($lang->http_referers, "index.php?module=tools-referers");

$plugins->run_hooks("admin_tools_referers_begin");


$page->output_header($lang->adminmenu_referers);

// Generate new Table
$table = new Table;
// Generate table headers
$table->construct_header($lang->table_head_link, array('width' => '60%'));
$table->construct_header($lang->table_head_user, array('width' => '22%'));
$table->construct_header($lang->table_head_time, array('width' => '18%'));

// read max referers to show
$maxReferers = $mybb->settings['showrefererslimit'];

// SQL query for read the last X referers from database
$query = $db->query("
	SELECT r.*, u.username, u.usergroup, u.displaygroup
	FROM ".TABLE_PREFIX.REFTABLE." r 
	LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=r.UserID)
	ORDER BY r.ID DESC
	LIMIT 0, " . $maxReferers . "
	");

while ($refitem = $db->fetch_array($query)) {
	$r = $refitem['TS'];
	$refitem['TS'] = substr($r,8,2) . "." . substr($r,5,2) . "." . substr($r,0,4) . " " . substr($r,11);
	
	$username = format_name($refitem['username'], $refitem['usergroup'], $refitem['displaygroup']);
	$userlink = build_profile_link($username, $refitem['UserID'], "_blank");
	$refitem['URL'] = '<a href="' . $refitem['URL'] . '" target="_blank">' . $refitem['URL'] . '</a>';
	
	if ($userlink == '') {
		$userlink = $lang->table_guest;
	}
	
	$userIp = my_inet_ntop($db->unescape_binary($refitem['BinaryUserIP']));
	
	// create new line for the referer to show
	$table->construct_cell($refitem['URL']);
	$table->construct_cell('<hover title="IP: ' . $userIp . '">' . $userlink . '</hover>');
	$table->construct_cell($refitem['TS']);
	$table->construct_row();
}

if ($table->num_rows() == 0) {
	$table->construct_cell($lang->table_no_entrys, array('colspan' => '3'));
	$table->construct_row();
}
$table->output($lang->adminmenu_referers);

$page->output_footer();
?>