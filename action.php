<?php
/**
 * DokuWiki Plugin custombuttons (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Constantinos Xanthopoulos <conx@xanthopoulos.info>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

/**
 * Add Event handler
 */
class action_plugin_custombuttons extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     */
    function register(Doku_Event_Handler $controller) {
        if($this->loadCBData())
            $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array());
    }

    /**
     * Read config
     *
     * @return bool|mixed
     */
    protected function loadCBData() {
        $json = new JSON(JSON_LOOSE_TYPE);
        $file = @file_get_contents(DOKU_PLUGIN . "custombuttons/config.json");
        if(!$file) return false;
        return $json->decode($file);
    }

    /**
     * Build list of buttons
     *
     * @return array
     */
    protected function makelist() {
        $conf = $this->loadCBData();

        $buttonlist = array();
        foreach($conf as $button) {
            $ico = '../../plugins/custombuttons/';
            if(!$button['icon']) {
                $ico .= 'genpng.php?text=' . $button["label"];
            } else {
                $ico .= 'ico/' . $button['icon'];
            }

            if($button["type"] == 1) {
                $buttonlist[] = array(
                    'type' => 'format',
                    'title' => $button["label"],
                    'icon' => $ico,
                    'open' => $button["pretag"],
                    'close' => $button["posttag"]
                );
            } else {
                $buttonlist[] = array(
                    'type' => 'insert',
                    'title' => $button["label"],
                    'icon' => $ico,
                    'insert' => $button["code"],
                    'block' => true
                );
            }
        }
        return $buttonlist;
    }

    /**
     * Add list with buttons to toolbar
     *
     * @param Doku_Event $event
     * @param            $param
     */
    public function insert_button(Doku_Event $event, $param) {
        $buttonlist = $this->makelist();

        if($this->getConf('usepicker')) {
            $event->data[] = array(
                'type' => 'picker',
                'title' => $this->getLang('picker'),
                'icon' => '../../plugins/custombuttons/custom.png',
                'list' => $buttonlist
            );
        } else {
            $event->data = array_merge($event->data, $buttonlist);
        }

    }
}

