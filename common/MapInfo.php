<?php

require_once(CARTOCOMMON_HOME . 'common/Serializable.php');

class CartocommonException extends Exception {

}

class LayerBase extends Serializable {
    
    public $id;
    public $label;
    public $minScale = 0;
    public $maxScale = 0;
    public $icon = "none";
    
    function unserialize($struct) {
        $this->id    = self::unserializeValue($struct, 'id'); 
        $this->label = self::unserializeValue($struct, 'label');

        // Not implemented
        //$this->minScale = $struct->minscale;
        //$this->maxScale = $struct->maxscale;
        //$this->icon     = $struct->icon;
    }
}

class LayerContainer extends LayerBase {
    public $children = array();
    
    function unserialize($struct) {
        parent::unserialize($struct);   
        $this->children = self::unserializeArray($struct, 'children');
    }    
}

class LayerGroup extends LayerContainer {

}

class Layer extends LayerContainer {

    public $msLayer;

    function unserialize($struct) {
        parent::unserialize($struct);
        $this->msLayer = self::unserializeValue($struct, 'msLayer'); 
    }
}

class LayerClass extends LayerBase {
    public $name;
    
    function unserialize($struct) {
        parent::unserialize($struct);
        $this->name = self::unserializeValue($struct, 'name');
    }
}

class Location extends Serializable {
    public $bbox;
    
    function unserialize($struct) {
        $this->bbox = self::unserializeObject($struct, 'bbox', 'Bbox');
    }
}

class InitialLocation extends Location {

}

class LayerState extends Serializable {
    public $id;
    public $hidden;
    public $selected;
    public $folded;

    function unserialize($struct) {
        $this->id       = self::unserializeValue($struct, 'id');
        $this->hidden   = self::unserializeValue($struct, 'hidden', 'boolean');
        $this->selected = self::unserializeValue($struct, 'selected', 'boolean');
        $this->folded   = self::unserializeValue($struct, 'folded', 'boolean');        
    }
}

class InitialMapState extends Serializable {

    public $id;
    public $location;
    public $layers;

    function unserialize($struct) {
        $this->id       = self::unserializeValue($struct, 'id');
        $this->location = self::unserializeObject($struct, 'location', 'InitialLocation');
        $this->layers   = self::unserializeObjectMap($struct, 'layers', 'LayerState'); 
    }
}

class MapInfo extends Serializable {
    public $mapId;
    public $mapLabel;
    public $layers;
    public $initialMapStates;
    public $extent;
    public $location;

    function unserialize($struct) {
        $this->mapId            = self::unserializeValue($struct, 'mapId');
        $this->mapLabel         = self::unserializeValue($struct, 'mapLabel');
  
        $this->loadPlugins = self::unserializeArray($struct, 'loadPlugins');
  
        // Layers class names are specicified in className attribute
        $this->layers           = self::unserializeObjectMap($struct, 'layers');
        $this->initialMapStates = self::unserializeObjectMap($struct, 'initialMapStates', 'InitialMapState');
        $this->extent           = self::unserializeObject($struct, 'extent', 'Bbox');
        $this->location         = self::unserializeObject($struct, 'location', 'Location');
    }
    
    function getLayerById($layerId) {

        foreach ($this->layers as $layer) {
            if ($layer->id == $layerId)
                return $layer;
        }
        return NULL;
    }

    function getInitialMapStateById($mapStateId) {

        foreach ($this->initialMapStates as $mapState) {
            if ($mapState->id == $mapStateId)
                return $mapState;
        }
        return NULL;
    }

    function getLayers() {

        return $this->layers;
    }

    function addChildLayerBase($parentLayer, $childLayer) {
        
        $childLayerId = $childLayer->id;

        if (in_array($childLayerId, array_keys($this->layers)))
            throw new CartocommonException("Trying to replace layer " . $childLayerId);

        if (!in_array($childLayerId, $parentLayer->children))
            $parentLayer->children[] = $childLayerId;

        $this->layers[$childLayerId] = $childLayer;
    }
}

?>