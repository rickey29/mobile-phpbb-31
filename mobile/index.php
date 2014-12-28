<?php
/*
    project: Mobile phpBB 3.1 (MphpBB31)
    file:    $phpbb_root_path/mobile/index.php
    version: 1.0
    author:  Rickey Gu
    web:     http://flexplat.com
    email:   rickey29@gmail.com
*/

if ( !defined('IN_PHPBB') )
{
    exit;
}


if ( $this->data['is_bot'] || defined('ADMIN_START') || defined('IN_ADMIN') )
{
    return;
}

if ( $style_id )
{
    return;
}


define('IN_MPHPBB31', true);

// library
require($phpbb_root_path . 'mobile/lib/lib.' . $phpEx);


global $user, $request;


$m_data = array();
$m_data['user_agent'] = $request->server('HTTP_USER_AGENT');
$m_data['accept'] = $request->server('HTTP_ACCEPT');
$m_data['profile'] = $request->server('HTTP_PROFILE');
$m_data['redirection'] = $request->variable('m-redirection', '');
$m_data['cookie'] = $request->variable($config['cookie_name'] . '_m-redirection', '', true, \phpbb\request\request_interface::COOKIE);

$m_value = m_get_redirection($m_data);

$m_response = array();
$m_response['device_platform'] = $m_value[0];
$m_response['device_grade'] = $m_value[1];
$m_response['device_system'] = $m_value[2];
$m_response['echo_page'] = $m_value[3];
$m_response['set_cookie'] = $m_value[4];


if ( !empty($m_response['set_cookie']) )
{
    if ( $m_response['set_cookie'] == 'mobile' )
    {
        $user->set_cookie('m-redirection', 'mobile', time()+31536000);
    }
    else if ( $m_response['set_cookie'] == 'desktop' )
    {
        $user->set_cookie('m-redirection', 'desktop', time()+31536000);
    }
}

if ( !empty($m_response['echo_page']) && $m_response['echo_page'] == 'mobile' )
{
    if ( $m_response['device_system'] == 'touch-phone' )
    {
        define('MPHPBB31', 'jQuery-Mobile touch-phone');
    }
    else if ( $m_response['device_system'] == 'tablet' )
    {
        define('MPHPBB31', 'jQuery-Mobile tablet');
    }
    else
    {
        define('MPHPBB31', 'jQuery-Mobile smartphone');
    }
    
    
    $sql = 'SELECT style_id
        FROM ' . STYLES_TABLE . "
        WHERE style_name = '" . $db->sql_escape(MPHPBB31) . "'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);
    
    $style_id = $row['style_id'];
}
?>
