<?php
/**
 * @package Common
 * @version $Id$
 */

/**
 * Project handler
 */
require_once(CARTOCLIENT_HOME . 'common/ProjectHandler.php');

/**
 * Project handler for the client
 *
 * Project name is know by environment variable CW3_PROJECT.
 * @package Common
 */
class ClientProjectHandler extends ProjectHandler {

    const PROJECT_ENV_VAR = 'CW3_PROJECT';

    function getProjectName () {
        $projectFileName = CARTOCLIENT_HOME . 'current_project.txt';
        if (is_readable($projectFileName))
            return rtrim(file_get_contents($projectFileName));
        
        if (array_key_exists(self::PROJECT_ENV_VAR, $_SERVER))
            return $_SERVER[self::PROJECT_ENV_VAR];
                
        if (array_key_exists('REDIRECT_' . self::PROJECT_ENV_VAR, $_SERVER))
            return $_SERVER['REDIRECT_' . self::PROJECT_ENV_VAR];
        
        return NULL;
    }

}

function smartyResource ($params, $text, &$smarty) {
    
    $text = stripslashes($text);
    
    if (isset($params['type'])) {
        $type = $params['type'];
        unset($params['type']);       
    }
    
    if (isset($params['plugin'])) {
        $plugin = $params['plugin'];
        unset($params['plugin']);        
    }

    if (isset($type)) {
        $text = $type . '/' . $text;
    }   
    if (isset($plugin)) {
        $text = $plugin . '/' . $text;
    }

    $projectHandler = new ClientProjectHandler();
    $text = $projectHandler->getWebPath(CARTOCLIENT_HOME, $text);

    return $text;
}

?>
