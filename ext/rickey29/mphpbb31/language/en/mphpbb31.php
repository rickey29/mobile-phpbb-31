<?php
/*
	project: Mobile phpBB 3.1 (MphpBB31)
	file:    $phpbb_root_path/ext/rickey29/mphpbb31/language/en/mphpbb31.php
	version: 2.0.0
	author:  Rickey Gu
	web:     http://flexplat.com
	email:   rickey29@gmail.com
*/

if ( !defined('IN_PHPBB') )
{
	exit;
}


if ( empty($lang) || !is_array($lang) )
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'PERSONAL_FOOTER' => '<h4>Powered by <a href="http://flexplat.com/" rel="external">FlexPlat</a>, <a href="https://www.phpbb.com/" rel="external">phpBB</a> and <a href="http://jquerymobile.com/" rel="external">jQuery Mobile</a></h4>',
	'PERSONAL_HEADER' => '',
	'SWITCH_TO_DESKTOP_STYLE' => 'Switch to desktop style',
));
?>