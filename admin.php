<?php
/**
 * DokuWiki Plugin custombuttons (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Xanthopoulos Constantinos <conx@xanthopoulos.info>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'admin.php';

class admin_plugin_custombuttons extends DokuWiki_Admin_Plugin
{
	function getInfo(){
		return array(
			'author' => 'Xanthopoulos Constantinos',
			'email'  => 'conx@xanthopoulos.info',
			'date'   => '2010-7-21',
			'name'   => 'CustomButtons',
			'desc'   => 'A plugin for adding custom buttons to the toolbar, to shortcut commonly used code blocks.',
			'url'    => 'http://www.dokuwiki.org/plugin:custombuttons',
		);
	}

	
	function forAdminOnly()
	{
		 return true;
	}

	function handle()
	{
		global $config_cascade;
	
		if (isset($_REQUEST['config']))
	       	{
			$fh = fopen(DOKU_PLUGIN."custombuttons/config.ini", 'w');
			
			if($fh)
			{
				fwrite($fh, $_REQUEST["config"]);
			}
			fclose($fh);
			@touch(reset($config_cascade['main']['local']));
		}
	}

	function html()
	{
		global $ID;
	
		ptln('<h1>Custom Buttons Configurations</h1>');
		ptln('<div>Add your custom buttons in the following format:<br><pre class="code">&lt;name&gt; = &lt;Shortcut code&gt;<br></pre>Example:</br><pre class="code">syntax =  "[[wiki:syntax|syntax]]"</pre> </div>')
		ptln('<form name="frm" action="'.wl($ID).'" method="post">');
		ptln('<input type="hidden" name="do"   value="admin" />');
		ptln('<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
		ptln('<input type="hidden" name="id" value="'.$ID.'" />');
		ptln('<textarea rows=10 cols=50 name="config">');
		echo file_get_contents(DOKU_PLUGIN."custombuttons/config.ini");
		ptln('</textarea>');
		ptln('<br><input type=submit value="Save"></form>');
	}
}

// vim:ts=4:sw=4:et:enc=utf-8:
