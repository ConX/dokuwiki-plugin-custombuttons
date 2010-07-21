<?php
/**
 * DokuWiki Plugin cstbtn (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Xanthopoulos Constantinos <conx@xanthopoulos.info>
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
		if (file_exists(DOKU_PLUGIN."custombuttons/config.ini"))
		{
			$controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button');
		}
        }

	function parse_ini()
	{
		$ini_array = parse_ini_file(DOKU_PLUGIN."custombuttons/config.ini");
		$list_array = array();
		foreach ($ini_array as $key => $value)
		{
			$list_array[$value] = '../../plugins/custombuttons/genpng.php?text='.$key;
		}
		return $list_array;
	}

        function insert_button(&$event, $param)
	{
		$list_array = $this->parse_ini();
                $event->data[] = array (
                        'type' => 'picker',
                        'title' => 'Custom Buttons',
			'icon' => '../../plugins/custombuttons/genpng.php?text=Custom',
			//'icon' => '../../plugins/custombuttons/picker.png',
                        'list' => $list_array
                );
        }
}

// vim:ts=4:sw=4:et:enc=utf-8:
