<?php
/**
 * Main plugin file
 * © 2018 Florian Berger
 * Last change: 2018-02-17
 */

if(!defined('IN_MYBB')) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('parse_message', 'steam_game');

function steamgamewidget_info() {
    global $lang;
    $lang->load('steamgamewidget');

    $desc = $lang->steamgamewidget_desc . '<br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="X9ULXRYHP84ZY">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>';

    return array(
        "name" 			=> $lang->steamgamewidget_name,
        "description"	=> $desc,
        'website'		=> 'http://community.mybb.com/user-75209.html',
        'author'		=> 'Florian Berger',
        "authorsite"	=> 'https://florian-berger.info',
        "version"		=> '1.0.0',
        "compatibility" => '18*',
        'guid'			=> 'f881f071e6ed47feaa5cfeb9ce4e1ba2',
        "codename"		=> 'berger_florian_steamgamewidget'
    );
}

function steamgamewidget_install() {
    global $db, $lang;
    $lang->load('steamgamewidget');

    $optionGroup = array(
        'name' => 'steamgame_widget',
        'title' => $db->escape_string($lang->settingsgroup_title),
        'description' => $db->escape_string($lang->settingsgroup_desc),
        'disporder' => 33,
        'isdefault' => 0
    );
    $groupid = $db->insert_query("settinggroups", $optionGroup);

    $maxWidthSetting = array(
        'name' => 'steamgame_widget_max_width',
        'title' => $db->escape_string($lang->setting_max_width_title),
        'description' => $db->escape_string($lang->setting_max_width_desc),
        'optionscode' => 'numeric',
        'value' => 0,
        'disporder' => 1,
        'gid' => $groupid
    );
    $db->insert_query("settings", $maxWidthSetting);

    rebuild_settings();
}

function steamgamewidget_uninstall() {
    global $db;

    $db->delete_query("settings", "name IN('steamgame_widget_max_width')");
    $db->delete_query("settinggroups", "name IN('steamgame_widget')");

    rebuild_settings();
}

function steamgamewidget_is_installed()
{
    global $db;

    $result = $db->query("SELECT gid FROM `" . TABLE_PREFIX . "settinggroups` WHERE name='steamgame_widget'");
    if ($result->num_rows > 0) {
        return true;
    }

    return false;
}

function steam_game($message) {
    return preg_replace("#\[steamgame](.*?)\[/steamgame\]#ei", "steamgame_parse('$1')", $message);
}

function steamgame_parse($gameId) {
    global $mybb;

    $gameId = filter_var($gameId, FILTER_SANITIZE_STRING);

    $widthSettingsValue = $mybb->settings['steamgame_widget_max_width'];
    if ($widthSettingsValue == 0) {
        return '<iframe src="http://store.steampowered.com/widget/' . $gameId . '" frameborder="0" style="width: 100%; height: 190px;"></iframe>';
    }

    return '<iframe src="http://store.steampowered.com/widget/' . $gameId . '" frameborder="0" style="width: ' . $widthSettingsValue . 'px; height: 190px;"></iframe>';
}

?>