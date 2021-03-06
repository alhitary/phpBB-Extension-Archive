<?php
/**
*
* Archive extension for the phpBB Forum Software package
*
* @copyright (c) 2014 o0johntam0o
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace o0johntam0o\archive\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	protected $helper, $template, $user, $config, $request, $php_ext;
	
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\config\config $config, \phpbb\request\request $request, $php_ext)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->config = $config;
		$this->request = $request;
		$this->php_ext = $php_ext;
	}
	
	static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup'		=> 'load_language_on_setup',
            'core.page_header'		=> 'assign_archive_link',
        );
    }
	
    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'o0johntam0o/archive',
            'lang_set' => 'archive',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function assign_archive_link($event)
    {
		if (!isset($this->config['archive_enable']) || !$this->config['archive_enable'])
		{
			return;
		}
		
		if ($this->user->page['page_dir'] != '' || str_replace('.' . $this->php_ext, '', $this->user->page['page_name']) == 'archive')
		{
			return;
		}
		
		$this->template->assign_var('U_ARCHIVE_AVAILABLE', true);
		
		if ($this->request->variable('f', 0) > 0)
		{
			if ($this->request->variable('t', 0) > 0)
			{
				$this->template->assign_var('U_ARCHIVE_PAGE', $this->helper->route('o0johntam0o_archive_viewtopic_controller', array('f' => $this->request->variable('f', 0), 't' => $this->request->variable('t', 0))));
			}
			else
			{
				$this->template->assign_var('U_ARCHIVE_PAGE', $this->helper->route('o0johntam0o_archive_viewforum_controller', array('f' => $this->request->variable('f', 0))));
			}
		}
		else
		{
			$this->template->assign_var('U_ARCHIVE_PAGE', $this->helper->route('o0johntam0o_archive_base_controller'));
		}
    }
}
