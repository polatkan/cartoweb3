<?php
/**
 * @package Tests
 * @version $Id$
 */

/**
 * @package Tests
 */
class ClientProjectplugin extends ClientPlugin {

    const PROJECTPLUGIN_INPUT = 'projectplugin_input';
    
    private $log;
    private $message;
    private $count;

    function __construct() {
        parent::__construct();

        $this->log =& LoggerManager::getLogger(__CLASS__);
    }

    function loadSession($sessionObject) {
        $this->count = $sessionObject;
    }

    function createSession(MapInfo $mapInfo, InitialMapState $initialMapState) {
        $this->count = 0;
    }
    function saveSession() {
        return $this->count;
    }
    
    function handleHttpRequest($request) {
        $this->count = $this->count + 1;
    }

    function buildMapRequest($mapRequest) {
        $request = new ProjectpluginRequest();
        if (array_key_exists(self::PROJECTPLUGIN_INPUT, $_REQUEST)) { 
            $request->message = $_REQUEST[self::PROJECTPLUGIN_INPUT];
        } else {
            $request->message = '';
        }
        $mapRequest->projectpluginRequest = $request;
    }

    function handleMapResult($mapResult) {
        $result = Serializable::unserializeObject($mapResult, 'projectpluginResult', 'ProjectpluginResult');
        $this->message = $result->shuffledMessage;
    }

    function renderForm($template) {
        if (!$template instanceof Smarty) {
            throw new CartoclientException('unknown template type');
        }
        
        $template->assign('projectplugin_active', true);
        $template->assign('projectplugin_message', "message: " . $this->message . 
                          " count: " . $this->count);
    }
}
?>