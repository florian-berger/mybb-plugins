<?php
// Main Plugin file for the plugin Prism Syntax Highlighting
// © 2016 Florian Berger
// ----------------------------------------
// Last Update: 2016-11-14

if(!defined('IN_MYBB')) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define("MYCODE_NAME", "Prism Code-Highlighting");

$plugins->add_hook('pre_output_page', 'add_scripts_to_output');

function syntaxhighlighting_info()
{
    global $lang;
    $lang->load('syntaxhighlighting');

    return array
    (
        'name'			=> $lang->syntaxhighlighting_info_name,
        'description'	=> $lang->syntaxhighlighting_info_desc,
        'website'		=> 'http://community.mybb.com/user-75209.html',
        'author'		=> 'Florian Berger',
        "authorsite"	=> 'https://florian-berger.info',
        'version'		=> '1.0.0',
        'compatibility' => '18*',
        'guid'			=> '123fab94fb6b3d7a6ebaf589f1c8291f',
        'codename' 		=> 'berger_florian_syntaxhighlighting'
    );
}

function syntaxhighlighting_install() {
    global $db, $mybb, $lang;

    global $lang;
    $lang->load('syntaxhighlighting');

    // Add MyCode

    $db->insert_query("mycode", array(
        "title" => MYCODE_NAME,
        "description" => $lang->myCode_Description,
        "regex" => "\\\\[code-sh=(.*?)\\\\](.*?)\\\\[/code-sh\\\\]",
        "replacement" => '<pre class="line-numbers"><code class="language-$1">$2</code></pre>',
        "active" => 1,
        "parseorder" => 0
    ));
}

function syntaxhighlighting_is_installed()
{
    global $db;

    // Check if MyCode exists
    if ($db->affected_rows($db->simple_select("mycode", "*", "title='" . MYCODE_NAME . "'")) > 0)
        return true;

    return false;
}

function syntaxhighlighting_uninstall()
{
    global $db;

    // Remove MyCode
    $db->delete_query("mycode", "title='" . MYCODE_NAME . "'");
}

function add_scripts_to_output($site)
{
    global $mybb;

    $site = str_replace('</head>', '<link type="text/css" rel="stylesheet" href="' . $mybb->settings['bburl'] . '/cache/themes/prism.css" /></head>', $site);
    $site = str_replace('</body>', '<script type="text/javascript" src="' . $mybb->settings['bburl'] . '/jscripts/prism.js"></script></body>', $site);

    return $site;
}

?>