<?php
/**
 * DokuWiki Plugin cstbtn (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author Constantinos Xanthopoulos <conx@xanthopoulos.info>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'action.php';

class action_plugin_custombuttons extends DokuWiki_Action_Plugin 
{

        function register(&$controller)
        {
		if (file_exists(DOKU_PLUGIN."custombuttons/config.json"))
			$controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array());
        }

	function loadCBData()
	{
		if (! $file = file_get_contents(DOKU_PLUGIN."custombuttons/config.json"))
			return false;
		$cbconf = json_decode($file,TRUE);
		return $cbconf;
	}
	
	function makelist()
	{
		$conf = $this->loadCBData();
		$buttonlist = array();
		foreach ($conf as $button)
		{
			if ($button["type"] == 1)
			{
				$buttonlist[] =  array(
					'type' => 'format',
					'title' => $button["label"],
					'icon' => '../../plugins/custombuttons/genpng.php?text='.$button["label"],
					'open' => $button["pretag"],
					'close' => $button["posttag"]);
			}
			else
			{
				$buttonlist[] =  array(
					'type'   => 'insert',
					'title'  =>  $button["label"],
					'icon'   =>  '../../plugins/custombuttons/genpng.php?text='.$button["label"],
					'insert' => $button["code"],
					'block'  => true);
			}
		}
		return $buttonlist;
	}

        function insert_button(&$event, $param)
	{
		$buttonlist = $this->makelist();
                $event->data[] = array (
                        'type' => 'picker',
                        'title' => 'Custom Buttons',
			'icon' => '../../plugins/custombuttons/custom.png',
                        'list' => $buttonlist
                );
        }
}

// vim:ts=4:sw=4:et:enc=utf-8:
