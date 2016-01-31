<?php
/*
	project: Mobile phpBB 3.1 (MphpBB31)
	file:    $phpbb_root_path/ext/rickey29/mphpbb31/event/listener.php
	version: 2.0.0
	author:  Rickey Gu
	web:     http://flexplat.com
	email:   rickey29@gmail.com
*/

namespace rickey29\mphpbb31\event;

if ( !defined('IN_PHPBB') )
{
	exit;
}

use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class listener implements EventSubscriberInterface
{
	private $msg_text;


	protected $db;

	private $request;
	private $user;
	private $config;
	private $template;

	private $root_path;
	private $php_ext;

	private $detection;


	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\request\request_interface $request, \phpbb\user $user, \phpbb\config\config $config, \phpbb\template\template $template, $root_path, $php_ext, $detection)
	{
		$this->msg_text = '';


		$this->db = $db;

		$this->request = $request;
		$this->user = $user;
		$this->config = $config;
		$this->template = $template;

		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->detection = $detection;
	}

	public function __destruct()
	{
	}


	static public function getSubscribedEvents()
	{
		return array(
			'core.common' => 'common',
			'core.login_box_failed' => 'login_box_failed',
			'core.page_footer_after' => 'page_footer_after',
			'core.page_header_after' => 'page_header_after',
			'core.user_setup' => 'user_setup',
			'core.viewtopic_modify_post_row' => 'viewtopic_modify_post_row',
		);
	}


	public function error_handler($errno, $msg_text, $errfile, $errline)
	{
		$this->msg_text = $msg_text;

		if ( defined('PHPBB_MSG_HANDLER') )
		{
			eval(PHPBB_MSG_HANDLER . '($errno, $msg_text, $errfile, $errline);');
		}
		else
		{
			msg_handler($errno, $msg_text, $errfile, $errline);
		}
	}


	public function common($event)
	{
		set_error_handler(array(&$this, 'error_handler'));
	}

	public function login_box_failed($event)
	{
		if ( !defined('MPHPBB31') )
		{
			return;
		}

		$pattern = '#<a[^>]*>\s*(.*)\s*</a>#i';
		$event['err'] = preg_replace($pattern, '$1', $event['err']);
	}

	public function page_footer_after($event)
	{
		if ( !defined('MPHPBB31') )
		{
			return;
		}

		$this->template->assign_vars(array(
			'PERSONAL_FOOTER' => !empty($this->user->lang['PERSONAL_FOOTER']) ? $this->user->lang['PERSONAL_FOOTER'] : '',
		));


		if ( empty($this->msg_text) )
		{
			return;
		}

		global $msg_title;

		$msg_text = $this->msg_text;
		$this->msg_text = '';

		$msg_title = !isset($msg_title) ? $this->user->lang['INFORMATION'] : ( !empty($this->user->lang[$msg_title]) ? $this->user->lang[$msg_title] : $msg_title );
		$msg_text = !empty($this->user->lang[$msg_text]) ? $this->user->lang[$msg_text] : $msg_text;

		$this->template->assign_vars(array(
			'MESSAGE_TITLE' => $msg_title,
		));

		$pattern = '#\s*<br\s/>\s*<br\s/>\s*#i';
		$pattern2 = '#^(.*)<a[^>]*\shref\s*=\s*("|\')([^\\2]*)\\2[^>]*>\s*(.*)\s*</a>(.*)$#i';
		foreach ( preg_split($pattern, $msg_text) as $key => $value )
		{
			$value = trim($value);

			if ( $key == 0 )
			{
				$this->template->assign_vars(array(
					'TEXT_HEADING' => $value
				));

				continue;
			}

			if ( preg_match($pattern2, $value, $matches) )
			{
				$this->template->assign_block_vars('msg_text_row', array(
					'TEXT' => $matches[1] . $matches[4] . $matches[5],
					'U_TEXT' => $matches[3]
				));
			}
			else
			{
				$this->template->assign_block_vars('msg_text_row', array(
					'TEXT' => $value
				));
			}
		}
	}

	public function page_header_after($event)
	{
		if ( !defined('MPHPBB31') )
		{
			return;
		}

		$this->template->assign_vars(array(
			'PERSONAL_HEADER' => !empty($this->user->lang['PERSONAL_HEADER']) ? $this->user->lang['PERSONAL_HEADER'] : '',
			'U_SWITCH_TO_DESKTOP_STYLE' => append_sid("{$this->root_path}index.$this->php_ext", 'm-redirection=desktop'),
		));
	}

	public function user_setup($event)
	{
		if ( defined('ADMIN_START') || defined('IN_ADMIN') )
		{
			return;
		}

		if ( $event['style_id'] )
		{
			return;
		}


		$redirection = $this->request->variable('m-redirection', '');
		if ( !empty($redirection) && $redirection != 'mobile' )
		{
			// make the cookie expires in a year time: 60 * 60 * 24 * 365 = 31,536,000
			$this->user->set_cookie('m_style', 'desktop', time() + 31536000);

			return;
		}

		$style = $this->request->variable($this->config['cookie_name'] . '_m_style', '', true, \phpbb\request\request_interface::COOKIE);
		if ( empty($redirection) && !empty($style) && $style == 'desktop' )
		{
			return;
		}

		if ( empty($style) || $style == 'desktop' )
		{
			$data = array();
			$data['user_agent'] = $this->request->server('HTTP_USER_AGENT');
			$data['accept'] = $this->request->server('HTTP_ACCEPT');
			$data['profile'] = $this->request->server('HTTP_PROFILE');

			$device = $this->detection->get_device($data);

			if ( $device == 'desktop' || $device == 'bot' )
			{
				// make the cookie expires in a year time: 60 * 60 * 24 * 365 = 31,536,000
				$this->user->set_cookie('m_style', 'desktop', time() + 31536000);

				return;
			}

			// make the cookie expires in a year time: 60 * 60 * 24 * 365 = 31,536,000
			$this->user->set_cookie('m_style', 'mobile', time() + 31536000);
		}


		$style = 'Mobile phpBB 3.1';
		$sql = 'SELECT style_id
			FROM ' . STYLES_TABLE . "
			WHERE style_active = 1 
				AND style_name = '" . $this->db->sql_escape($style) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ( empty($row) )
		{
			return;
		}

		define('MPHPBB31', 'Mobile');

		$event['style_id'] = $row['style_id'];


		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'rickey29/mphpbb31',
			'lang_set' => 'mphpbb31',
		);

		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function viewtopic_modify_post_row($event)
	{
		if ( !defined('MPHPBB31') )
		{
			return;
		}

		$post_row = $event['post_row'];

		$pattern = '#<a[^>]*\shref\s*=\s*("|\')([^\\1]*)\\1[^>]*>\s*(.*)\s*</a>#i';
		$post_row['MESSAGE'] = preg_replace($pattern, '<a href="$2" rel="external">$3</a>', $post_row['MESSAGE']);

		$pattern = '#<a[^>]*>\s*(.*)\s*</a>#i';
		$post_row['L_POST_DELETED_MESSAGE'] = preg_replace($pattern, '$1', $post_row['L_POST_DELETED_MESSAGE']);
		$post_row['L_IGNORE_POST'] = preg_replace($pattern, '$1', $post_row['L_IGNORE_POST']);

		$event['post_row'] = $post_row;
	}
}
?>