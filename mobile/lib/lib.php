<?php
/*
    project: Mobile phpBB 3.1 (MphpBB31)
    file:    $phpbb_root_path/mobile/lib/lib.php
    version: 1.0
    author:  Rickey Gu
    web:     http://flexplat.com
    email:   rickey29@gmail.com
*/

if ( !defined('IN_PHPBB') )
{
    exit;
}

if ( !defined('IN_MPHPBB31') )
{
    exit;
}


function m_get_detection($data)
{
    $device_list = array(
        // Apple iOS
        'iPad' => array(
            'Apple iPad',
            'jQuery Mobile A-grade',
            'tablet'
        ),
        'iPhone' => array(
            'Apple iPhone',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        'iPod' => array(
            'Apple iPod',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        
        // Kindle Fire
        'Kindle Fire' => array(
            'Kindle Fire',
            'jQuery Mobile A-grade',
            'tablet'
        ),
        'Kindle/' => array(
            'Kindle',
            'jQuery Mobile A-grade',
            'tablet'
        ),
        
        // Android
        'Android*Mobile' => array(
            'Android',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        'Android' => array(
            'Android Honeycomb',
            'jQuery Mobile A-grade',
            'tablet'
        ),
        
        // Chrome
        'Chrome/' => array(
            'Chrome',
            'jQuery Mobile A-grade',
            'desktop'
        ),
        
        // Macintosh
        'Macintosh' => array(
            'Macintosh',
            'jQuery Mobile A-grade',
            'desktop'
        ),
        
        // Firefox
        'Firefox/' => array(
            'Firefox',
            'jQuery Mobile A-grade',
            'desktop'
        ),
        
        // Windows Phone
        'Windows Phone' => array(
            'Windows Phone',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        
        // Windows Mobile
        'Windows CE' => array(
            'Windows Mobile',
            'jQuery Mobile C-grade',
            'smartphone'
        ),
        
        // Internet Explorer
        'MSIE ' => array(
            'Internet Explorer',
            'jQuery Mobile A-grade',
            'desktop'
        ),
        
        // Opera Mobile
        'Opera Mobi*Version/' => array(
            'Opera Mobile',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        
        // Opera Mini
        'Opera Mini/' => array(
            'Opera Mini',
            'jQuery Mobile B-grade',
            'smartphone'
        ),
        
        // Opera
        'Opera*Version/' => array(
            'Opera',
            'jQuery Mobile A-grade',
            'desktop'
        ),
        
        // Palm WebOS
        'webOS/*AppleWebKit' => array(
            'Palm WebOS',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        'TouchPad/' => array(
            'Palm WebOS Pad',
            'jQuery Mobile A-grade',
            'tablet'
        ),
        
        // Meego
        'MeeGo' => array(
            'Meego',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        
        // BlackBerry
        'BlackBerry*AppleWebKit*Version/' => array(
            'BlackBerry Touch-phone',
            'jQuery Mobile A-grade',
            'touch-phone'
        ),
        'PlayBook*AppleWebKit' => array(
            'BlackBerry Playbook',
            'jQuery Mobile A-grade',
            'tablet'
        ),
        'BlackBerry*/*MIDP' => array(
            'BlackBerry Smartphone',
            'jQuery Mobile C-grade',
            'smartphone'
        ),
        
        // Safari
        'Safari' => array(
            'Safari',
            'jQuery Mobile A-grade',
            'desktop'
        ),
        
        // Nokia Symbian
        'Symbian/' => array(
            'Nokia Symbian',
            'jQuery Mobile B-grade',
            'touch-phone'
        ),
        
        
        // Google
        'googlebot-mobile' => array(
            'Google Mobile Bot',
            'jQuery Mobile A-grade',
            'mobile-bot'
        ),
        'googlebot' => array(
            'Google Bot',
            '',
            'bot'
        ),
        
        // Microsoft
        'bingbot' => array(
            'Microsoft Bing',
            '',
            'bot'
        ),
        
        // Yahoo!
        'Yahoo! Slurp' => array(
            'Yahoo! Slurp',
            '',
            'bot'
        )
    );
    
    // application/vnd.wap.xhtml+xml
    $accept_list = array(
        'application/vnd.wap.xhtml+xml' => array(
            'unrecognized',
            'jQuery Mobile C-grade',
            'feature-phone'
        )
    );
    
    
    if ( !empty($data['user_agent']) )
    {
        foreach ( $device_list as $key => $value )
        {
            if ( preg_match('#' . str_replace('\*', '.*?', preg_quote($key, '#')) . '#i', $data['user_agent']) )
            {
                return $value;
            }
        }
    }
    
    if ( !empty($data['accept']) )
    {
        foreach ( $accept_list as $key => $value )
        {
            if ( preg_match('#' . str_replace('\*', '.*?', preg_quote($key, '#')) . '#i', $data['accept']) )
            {
                return $value;
            }
        }
    }
    
    if ( !empty($data['profile']) )
    {
        return array('unrecognized', 'jQuery Mobile C-grade', 'feature-phone');
    }
    
    if ( empty($data['user_agent']) )
    {
        return array('', '', 'desktop');
    }
    
    return array('unrecognized', 'unrecognized', 'unrecognized');
}

function m_get_redirection($data)
{
    list($device_platform, $device_grade, $device_system) = m_get_detection($data);
    if ( $device_system == 'desktop' || $device_system == 'bot' )
    {
        return array($device_platform, $device_grade, $device_system, 'desktop', '');
    }
    else if ( $device_system == 'mobile-bot' )
    {
        return array($device_platform, $device_grade, $device_system, 'mobile', '');
    }
    
    if ( !empty($data['redirection']) )
    {
        if ( $data['redirection'] == 'mobile' )
        {
            return array($device_platform, $device_grade, $device_system, 'mobile', 'mobile');
        }
        else if ( $data['redirection'] == 'desktop' )
        {
            return array($device_platform, $device_grade, $device_system, 'desktop', 'desktop');
        }
    }
    
    if ( !empty($data['cookie']) )
    {
        if ( $data['cookie'] == 'mobile' )
        {
            return array($device_platform, $device_grade, $device_system, 'mobile', 'mobile');
        }
        else if ( $data['cookie'] == 'desktop' )
        {
            return array($device_platform, $device_grade, $device_system, 'desktop', 'desktop');
        }
    }
    
    return array($device_platform, $device_grade, $device_system, 'mobile', '');
}


function m_update_address_list(&$address_list)
{
    if ( empty($address_list) || !is_array($address_list) )
    {
        return;
    }
    
    $pattern = '#^\s*<a[^>]*>\s*(.*)\s*</a>\s*$#isU';
    foreach ( $address_list as $key => $value )
    {
        if ( preg_match($pattern, $value, $matches) )
        {
            $address_list[$key] = $matches[1];
        }
    }
}

function m_update_err($err)
{
    $pattern = '#<a[^>]*>\s*(.*)\s*</a>#isU';
    $err = preg_replace($pattern, '$1', $err);
    
    return $err;
}

function m_update_msg_text($msg_text)
{
    global $template;
    
    $pattern = '#<br\s*/>\s*<br\s*/>#isU';
    $pattern2 = '#^(.*)<a.*\s+href\s*=\s*("|\')([^\\2]*)\\2.*>\s*(.*)\s*</a>(.*)$#isU';
    foreach ( preg_split($pattern, $msg_text) as $key => $value )
    {
        $value = trim($value);
        
        if ( $key == 0 )
        {
            $template->assign_vars(array(
                'TEXT_HEADING' => $value
            ));
            
            continue;
        }
        
        if ( preg_match($pattern2, $value, $matches) )
        {
            $template->assign_block_vars('msg_text_row', array(
                'TEXT' => $matches[1] . $matches[4] . $matches[5],
                'U_TEXT' => $matches[3]
            ));
        }
        else
        {
            $template->assign_block_vars('msg_text_row', array(
                'TEXT' => $value
            ));
        }
    }
}

function m_update_post_row(&$post_row)
{
    $pattern = '#<a[^>]*>\s*(.*)\s*</a>#isU';
    
    $post_row['L_POST_DELETED_MESSAGE'] = preg_replace($pattern, '$1', $post_row['L_POST_DELETED_MESSAGE']);
    $post_row['L_IGNORE_POST'] = preg_replace($pattern, '$1', $post_row['L_IGNORE_POST']);
    $post_row['L_POST_DISPLAY'] = preg_replace($pattern, '$1', $post_row['L_POST_DISPLAY']);
}
?>
