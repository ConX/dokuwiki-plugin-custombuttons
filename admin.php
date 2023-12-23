<?php
/**
 * DokuWiki Plugin custombuttons (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author Constantinos Xanthopoulos <conx@xanthopoulos.info>
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
    public function getMenuText($language) {
        return $this->getLang('name');
    }

    /**
     * Read config
     *
     * @return bool|mixed
     */
    protected function loadCBData() {
        $file = @file_get_contents(DOKU_PLUGIN.'custombuttons/config.json');
        if (!$file) return false;
        return json_decode($file, true);
    }

    /**
     * Store config
     *
     * @param $conf
     */
    protected function saveCBData($conf) {
        $configfile = DOKU_PLUGIN.'custombuttons/config.json';
        if (is_writable($configfile) || (!file_exists($configfile) && is_writable(DOKU_PLUGIN.'custombuttons'))) {
            file_put_contents($configfile, json_encode($conf));
        } else {
            msg($this->getLang('txt_error'), -1);
        }
    }

    protected function reloadBar() {
        touch(DOKU_CONF.'local.php');
    }

    /**
     * Execute the requested action
     */
    public function handle() {
        global $INPUT;

        if ($INPUT->has('add')) {
            if (!checkSecurityToken()) return;

            $conf = $this->loadCBData();
            if (!$conf) {
                $conf = array();
            }
            $type = 0;
            if ($INPUT->str('pretag') != '' || $INPUT->str('posttag') != '') {
                $type = 1;
            }
            $button = array(
                'label' => $INPUT->str('label'),
                'code' => $INPUT->str('code'),
                'type' => $type,
                'pretag' => $INPUT->str('pretag'),
                'posttag' => $INPUT->str('posttag'),
                'sample' => $INPUT->str('sample'),
                'icon' => $INPUT->str('icon')
            );
            $id = $INPUT->int('id', -1);
            if($id === -1) {
                $conf[] = $button;
            } else {
                //overwrite existing
                $conf[$id] = $button;
            }


            $this->saveCBData($conf);
            $this->reloadBar();
        } elseif ($INPUT->has('submitdelete') && $INPUT->has('select')) {
            if (!checkSecurityToken()) return;

            $conf = $this->loadCBData();
            unset($conf[$INPUT->int('select')]);
            $this->saveCBData($conf);
            $this->reloadBar();
        }
    }

    /**
     * Render HTML output
     */
    public function html() {
        global $ID, $INPUT;
        $conf = $this->loadCBData();

        echo '<div id="custombuttons">';
        echo '<h1>'.$this->getLang('name').'</h1>';

        // list of custom buttons
        echo '<h3>'.$this->getLang('btnslist').'</h3>';
        echo '<form id="cb_button_list" action="'.wl($ID).'" method="post">'
            .'<input type="hidden" name="do" value="admin" />'
            .'<input type="hidden" name="page" value="'.$this->getPluginName().'" />';

        formSecurityToken();

        echo '<table class = "inline">';
        echo '<tr>'
            .'<th>'.$this->getLang('btnslist_label').'</th>'
            .'<th>'.$this->getLang('btnslist_code').'</th>'
            .'<th>'.$this->getLang('btnslist_select').'</th>'
            .'</tr>';
        if ($conf) {
            foreach ($conf as $key => $button) {
                echo '<tr>';

                $icon = '';
                if ($button['icon']) {
                    $icon = '<img src="'. DOKU_BASE.'lib/plugins/custombuttons/ico/'.$button['icon'].'" /> ';
                }
                echo '<td>'.$icon.hsc($button['label']).'</td>';

                if ($button['type'] === 0) {
                    echo '<td>'.hsc($button['code']).'</td>';
                } else {
                    echo '<td>'.hsc($button['pretag']).hsc($button['sample'] ?? '').hsc($button['posttag']).'</td>';
                };
                echo '<td><input type="radio" name="select" value="'.$key.'"/></td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '<input type="submit" class="button" name="submitdelete" value="'.$this->getLang('btn_delete').'"/>';
        echo '<input type="submit" class="button" name="submitedit" value="'.$this->getLang('btn_edit').'"/>';
        echo '</form>';
        echo '</br></br>';

        $action = 'add';
        $id = -1; //no entry yet
        $button = [
            'label' => '',
            'icon' => '',
            'code' => '',
            'pretag' => '',
            'posttag' => '',
            'sample' => ''
        ];
        if($INPUT->has('submitedit') && $INPUT->has('select')) {
            $action = 'modify';
            $id = $INPUT->int('select', -1); //if not given, just create new
            $button = $conf[$id];
        }

        // add custom button form
        echo '<h3>'.$this->getLang($action . 'btn').'</h3>';
        echo '<form id="cb_add_button" action="'.wl($ID).'" method="post">'
            .'<input type="hidden" name="do" value="admin" />'
            .'<input type="hidden" name="add" value="1" />'
            .'<input type="hidden" name="id" value="' . $id . '" />'
            .'<input type="hidden" name="page" value="'.$this->getPluginName().'" />';
        formSecurityToken();
        echo '<table>';
        echo '<tr>';
        echo '<th>'.$this->getLang('addbtn_icon').'</th>';
        echo '<td>'
            .'<select name="icon">'
            .'<option value="">'.$this->getLang('addbtn_textonly').'</option>';
        $files = glob(dirname(__FILE__).'/ico/*.png');
        foreach ($files as $file) {
            $file = hsc(basename($file));
            $selected = $file === $button['icon'] ? ' selected' : '';
            echo '<option value="'.$file.'"' . $selected . '>'.$file.'</option>';
        };
        echo '</select>';
        echo '</td>';
        echo '<td></td>';
        echo '</tr>';
        echo '<tr>'
            .'<th>'.$this->getLang('addbtn_label').'</th>'
            .'<td><input type="text" name="label" value="' . hsc($button['label']) . '"/></td>'
            .'<td></td>'
            .'</tr>';
        echo '<tr>'
            .'<th>'.$this->getLang('addbtn_pretag').'</th>'
            .'<td><input type="text" name="pretag" value="' . hsc($button['pretag']) . '" /></td>'
            .'<td>*</td>'
            .'</tr>';
        echo '<tr>'
            .'<th>'.$this->getLang('addbtn_posttag').'</th>'
            .'<td><input type="text" name="posttag" value="' . hsc($button['posttag']) . '" /></td>'
            .'<td>*</td>'
            .'</tr>';
        echo '<tr>'
            .'<th>'.$this->getLang('addbtn_sample').'</th>'
            .'<td><input type="text" name="sample" value="' . hsc($button['sample']) . '" /></td>'
            .'<td>*</td>'
            .'</tr>';
        echo '<tr>'
            .'<th>'.$this->getLang('addbtn_code').'</th>'
            .'<td><input type="text" name="code" value="' . hsc($button['code']) . '" /></td>'
            .'<td></td>'
            .'</tr>';
        echo '</table>';
        echo '<input type="submit" class="button" value="'.$this->getLang('btn_' . $action).'" />';
        echo '</form>';
        echo '<div id="cb_comment">'.$this->getLang('txt_comment').'</div>';
        echo '</div>';
    }
}
