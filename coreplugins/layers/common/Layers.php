<?php
/**
 * @package CorePlugins
 * @version $Id$
 */
/**
 * Abstract serializable
 */
require_once(CARTOCOMMON_HOME . 'common/Serializable.php');

/**
 * A request for layers.
 *
 * @package CorePlugins
 */
class LayersRequest extends Serializable {
    public $layerIds;

    function unserialize($struct) {
        $this->layerIds = self::unserializeArray($struct, 'layerIds');
    }
}

/**
 * Result of a layers request. It is empty.
 *
 * @package CorePlugins
 */
class LayersResult {}

?>
