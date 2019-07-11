<?php
/**
 * DokuWiki Plugin custombuttons (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author Constantinos Xanthopoulos <conx@xanthopoulos.info>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

/**
 * Class admin_plugin_custombuttons
 */
class admin_plugin_custombuttons extends DokuWiki_Admin_Plugin {

	/**
	 * Return true for access only by admins (config:superuser) or false if managers are allowed as well
	 *
	 * @return bool
	 */
	public function forAdminOnly() {
		return true;
	}

	/**
	 * return prompt for admin menu
	 */
	function getMenuText($language) {
		return $this->getLang('name');
	}

	/**
	 * Read config
	 *
	 * @return bool|mixed
	 */
	protected function loadCBData() {
		$json = new JSON(JSON_LOOSE_TYPE);
		$file = @file_get_contents(DOKU_PLUGIN."custombuttons/config.json");
		if(!$file) return false;
		return $json->decode($file);
	}

	/**
	 * Store config
	 *
	 * @param $conf
	 */
	protected function saveCBData($conf) {
		$json = new JSON();
		$json = $json->encode($conf);
		$configfile = DOKU_PLUGIN."custombuttons/config.json";
		if(is_writable($configfile) || (!file_exists($configfile) && is_writable(DOKU_PLUGIN."custombuttons"))) {
			file_put_contents($configfile, $json);
		} else {
			msg($this->getLang('txt_error'), -1);
		}
	}

	protected function reloadBar() {
		touch(DOKU_CONF."local.php");
	}

	public function handle() {

		if (isset($_REQUEST['add'])) {
			if (!checkSecurityToken()) return;

			$conf = $this->loadCBData();
			if(!$conf) {
				$conf = array();
			}
			$type = 0;
			if ($_REQUEST["pretag"] != "" && $_REQUEST["posttag"] != "") {
				$type = 1;
			}
			array_push($conf, array(
				"label"	 => $_REQUEST["label"],
				"code"	  => $_REQUEST["code"],
				"type"	  => $type,
				"pretag"	=> $_REQUEST["pretag"],
				"posttag"   => $_REQUEST["posttag"],
				"icon"	  => $_REQUEST["icon"],
			));

			$this->saveCBData($conf);
			$this->reloadBar();
		} elseif (isset($_REQUEST['delete'])) {
			if (!checkSecurityToken()) return;

			$conf = $this->loadCBData();
			unset($conf[$_REQUEST["delete"]]);
			$this->saveCBData($conf);
			$this->reloadBar();
		}
	}

	public function html() {
		global $ID;
		$conf = $this->loadCBData();

		echo '
<div id = "custombuttons">
	<h1>'.$this->getLang ( 'name' ).'</h1>

	<h3>'.$this->getLang ( 'btnslist' ).'</h3>
	<form id = "cb_button_list" action = "'.wl ( $ID ).'" method = "post">
		<input type = "hidden" name = "do" value = "admin" />
		<input type = "hidden" name = "page" value = "'.$this->getPluginName ( ).'" />';
		formSecurityToken ( );
		echo '
		<table class = "inline">
			<tr>
				<th>'.$this->getLang ( 'btnslist_label' ).'</th>
				<th>'.$this->getLang ( 'btnslist_code' ).'</th>
				<th>'.$this->getLang ( 'btnslist_delete' ).'</th>
			</tr>';
		if ( $conf ) {
			foreach ( $conf as $key => $button ) {
				echo '
			<tr>';
				if ( !$button["type"] ) {
					echo '
				<td>'.hsc ( $button["label"] ).'</td>
				<td>'.hsc ( $button["code"] ).'</td>';
				} else {
					$icon = '';
					if ( $button['icon'] ) $icon = '<img src="' . DOKU_BASE.'lib/plugins/custombuttons/ico/'.$button['icon']. '" /> ';
					echo '
				<td>'.$icon.hsc ( $button["label"] ).'</td>
				<td>'.hsc ( $button["pretag"] ).hsc ( $button["code"] ).hsc ( $button["posttag"] ).'</td>';
				};
				echo '
				<td><input type = "radio" name = "delete" value = "'.$key.'"/></td>
			</tr>';
			}
		}
		echo '
		</table>
		<input type = "submit" class = "button" value = "'.$this->getLang ( 'btn_delete' ).'"/>
	</form>
     </br></br>
	
	<h3>'.$this->getLang('addbtn').'</h3>
	<form id = "cb_add_button" action = "'.wl ( $ID ).'" method = "post">
		<input type = "hidden" name = "do" value = "admin" />
		<input type = "hidden" name = "add" value = "1" />
		<input type = "hidden" name = "page" value = "'.$this->getPluginName ( ).'" />';
		formSecurityToken ( );
		echo '
		<table>
			<tr>
				<th>'.$this->getLang ( 'addbtn_icon' ).'</th>
				<td>
					<select name = "icon">
						<option value = "">'.$this->getLang ( 'addbtn_textonly' ).'</option>';
		$files = glob ( dirname ( __FILE__ ).'/ico/*.png' );
		foreach ( $files as $file ) {
			$file = hsc ( basename ( $file ) );
			echo '
						<option value = "'.$file.'">'.$file.'</option>';
		};
		echo '
					</select>
				</td>
				<td></td>
			</tr>
			<tr>
				<th>'.$this->getLang ( 'addbtn_label' ).'</th>
				<td>
					<input type = "text" name = "label" />
				</td>
				<td></td>
			</tr>
			<tr>
				<th>'.$this->getLang ( 'addbtn_pretag' ).'</th>
				<td>
					<input type = "text" name = "pretag" />
				</td>
				<td>*</td>
			</tr>
			<tr>
				<th>'.$this->getLang ( 'addbtn_posttag' ).'</th>
				<td>
					<input type="text" name = "posttag" />
				</td>
				<td>*</td>
			</tr>
			<tr>
				<th>'.$this->getLang ( 'addbtn_code' ).'</th>
				<td>
					<input type = "text" name = "code" />
				</td>
				<td></td>
			</tr>
		</table>
		<input type = "submit" class = "button" value = "'.$this->getLang ( 'btn_add' ).'" />
	</form>
	<div id = "cb_comment" >'.$this->getLang ( 'txt_comment' ).'</div>
</div>';
	}
}
