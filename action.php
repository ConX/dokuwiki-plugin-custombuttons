<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\Event;
use dokuwiki\Extension\EventHandler;

/**
 * DokuWiki Plugin custombuttons (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Constantinos Xanthopoulos <conx@xanthopoulos.info>
 */
class action_plugin_custombuttons extends ActionPlugin
{

    /**
     * Registers a callback function for a given event
     */
    public function register(EventHandler $controller)
    {
        if ($this->loadCBData()) {
            $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insertButton', []);
        }
    }

    /**
     * Read config
     *
     * @return bool|mixed
     */
    protected function loadCBData()
    {
        $file = @file_get_contents(DOKU_PLUGIN . "custombuttons/config.json");
        if (!$file) return false;
        return json_decode($file, true);
    }

    /**
     * Build list of buttons
     *
     * @return array
     */
    protected function makelist()
    {
        $conf = $this->loadCBData();

        $buttonlist = [];
        foreach ($conf as $button) {
            $ico = '../../plugins/custombuttons/';
            if (!$button['icon']) {
                $ico .= 'genpng.php?text=' . $button['label'];
            } else {
                $ico .= 'ico/' . $button['icon'];
            }

            if ($button['type'] == 1) {
                $buttonlist[] = [
                    'type' => 'format',
                    'title' => $button['label'],
                    'icon' => $ico,
                    'open' => $button['pretag'],
                    'close' => $button['posttag'],
                    'sample' => $button['sample'] ?? '',
                    'class' => $button['icon'] ? '' : 'textbutton'
                ];
            } else {
                $buttonlist[] = [
                    'type' => 'insert',
                    'title' => $button['label'],
                    'icon' => $ico,
                    'insert' => $button['code'],
                    'block' => true,
                    'class' => $button['icon'] ? '' : 'textbutton'
                ];
            }
        }
        return $buttonlist;
    }

    /**
     * Add list with buttons to toolbar
     *
     * @param Event $event
     */
    public function insertButton(Event $event)
    {
        $buttonlist = $this->makelist();

        if ($this->getConf('usepicker')) {
            $event->data[] = [
                'type' => 'picker',
                'title' => $this->getLang('picker'),
                'icon' => '../../plugins/custombuttons/custom.png',
                'list' => $buttonlist
            ];
        } else {
            $event->data = array_merge($event->data, $buttonlist);
        }
    }
}

