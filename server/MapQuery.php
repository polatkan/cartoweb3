<?php

/**
 * A class to perform queries based on a set of selected id's.
 * @package Server
 * @author Sylvain Pasche <sylvain.pasche@camptocamp.com> 
 */
class MapQuery {

    private function genericQueryString($idAttribute, $idType, $selectedIds) {
        // FIXME: does queryByAttributes support multiple id's for dbf ?
        $queryString = array();
        foreach($selectedIds as $id) {
            if ($idType == 'string')
                $queryString[] = "'$id'";
            else
                /* TODO */ 
                x('todo_int_query_string');
        } 
        return $queryString;
    }
    
    private function databaseQueryString($idAttribute, $idType, $selectedIds) {
        if ($idType != 'string')
            x('todo_database_int_query_string');
        $queryString = implode("','", $selectedIds);
        return array("$idAttribute in ('$queryString')");
    }

    private function isDatabaseLayer($msLayer) {
        return $msLayer->connectiontype == MS_POSTGIS;
    }

    private function queryLayerByAttributes(ServerContext $serverContext, 
                     $msLayer, $idAttribute, $query) { 
        $log =& LoggerManager::getLogger(__METHOD__);
        
        $log->debug("queryLayerByAttributes layer: $msLayer->name " .
                "idAttribute: $idAttribute query: $query");
        $ret = @$msLayer->queryByAttributes($idAttribute, $query, MS_MULTIPLE);
        if ($ret == MS_FAILURE) {
            throw new CartoserverException("Recentering query returned no " .
                    "results. Layer: $msLayer->name, idAttrubute: $idAttribute," .
                    " query: $query"); 
        }

        $serverContext->checkMsErrors();
        $msLayer->open();
        $results = array();
        for ($i = 0; $i < $msLayer->getNumResults(); $i++) {
            $result = $msLayer->getResult($i);
            $shape = $msLayer->getShape($result->tileindex, $result->shapeindex);

            $results[] = $shape;
        }
        $msLayer->close();
        return $results;
    }

    private function checkImplementedConnectionTypes($msLayer) {
    
        $implementedConnectionTypes = array(MS_SHAPEFILE, MS_POSTGIS);
        
        if (in_array($msLayer->connectiontype, $implementedConnectionTypes))
            return;
            
        throw new CartoserverException("Layer to center on has an unsupported " .
                "connection type: $msLayer->connectiontype");
    }

    private function decodeIds($ids) {
        return array_map('utf8_decode', $ids);
    }
    
    /**
     * Perform a query based on a set of selected id's on a given layer.
     * @param ServerContext Current server context
     * @param IdSelection The selection to use for the query. It contains a
     *                    layer  name and a set of id's
     */
    public function queryByIdSelection(ServerContext $serverContext, 
                                        IdSelection $idSelection) {

        $mapInfo = $serverContext->getMapInfo();
        $msLayer = $mapInfo->getMsLayerById($serverContext->getMapObj(), 
                                            $idSelection->layerId);
        
        $idAttribute = $idSelection->idAttribute;
        if (is_null($idAttribute)) {
            $idAttribute = $serverContext->getIdAttribute($idSelection->layerId);
        }
        if (is_null($idAttribute)) {
            throw new CartoserverException("can't find idAttribute for layer " .
                    "$idSelection->layerId");
        }
        $idType = $idSelection->idType;
        if (is_null($idType)) {
            $idType = $serverContext->getIdAttributeType($idSelection->layerId);
        }

        self::checkImplementedConnectionTypes($msLayer);

        $queryStringFunction = (self::isDatabaseLayer($msLayer)) ?
            'databaseQueryString' : 'genericQueryString';

        $ids = self::decodeIds($idSelection->selectedIds);

        // FIXME: can shapefiles support queryString for multiple id's ?
        //  if yes, then improve this handling. 

        $queryString = self::$queryStringFunction($idAttribute, $idType, $ids); 
        $results = array();
        foreach($queryString as $query) {
            $new_results = self::queryLayerByAttributes($serverContext, $msLayer, 
                                             $idAttribute, $query);
            $results = array_merge($results, $new_results);
        }
        return $results;
    }      
}
?>
