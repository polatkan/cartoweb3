<?php
/**
 * @package Tests
 * @version $Id$
 */

/**
 * Abstract test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';
require_once('client/CartoserverServiceWrapper.php');

require_once(CARTOCOMMON_HOME . 'coreplugins/query/common/Query.php');
require_once(CARTOCOMMON_HOME . 'coreplugins/layers/common/Layers.php');
require_once(CARTOCOMMON_HOME . 'common/BasicTypes.php');

/**
 * Unit test for server query plugin via webservice. 
 *
 * @package Tests
 * @author Sylvain Pasche <sylvain.pasche@camptocamp.com>
 */
class coreplugins_query_server_RemoteServerQueryTest
                    extends client_CartoserverServiceWrapper {

    function isTestDirect() {
        return true;   
    }
    
    private function getMapRequestAllLayers() {
    
        $queryRequest = new QueryRequest();
        $bbox = new Bbox();
        $bbox->setFromBbox(0, 51.5, 0, 51.5);
        $queryRequest->bbox = $bbox;
        $queryRequest->defaultTableFlags = new TableFlags();
        $queryRequest->defaultTableFlags->returnAttributes = true;
        $queryRequest->queryAllLayers = true;

        $mapRequest = $this->createRequest();
        $mapRequest->queryRequest = $queryRequest;        
        $mapRequest->layersRequest = new LayersRequest();
        $mapRequest->layersRequest->layerIds = 
                    array('polygon', 'line', 'point');
        
        return $mapRequest;
    }

    private function getMapRequestNoAttributes() {
    
        $mapRequest = $this->getMapRequestAllLayers();
        $mapRequest->queryRequest->defaultTableFlags->returnAttributes = false;        
        return $mapRequest;
    }

    private function getMapRequestUseInQuery() {
    
        $queryRequest = new QueryRequest();
        $bbox = new Bbox();
        $bbox->setFromBbox(0, 51.5, 0, 51.5);
        $queryRequest->bbox = $bbox;
        $queryRequest->queryAllLayers = false;
        $querySelections = array();
        $querySelection = new QuerySelection();
        $querySelection->layerId = 'polygon';
        $querySelection->useInQuery = true;
        $querySelections[] = $querySelection;
        $querySelection = new QuerySelection();
        $querySelection->layerId = 'line';
        $querySelection->useInQuery = true;
        $querySelections[] = $querySelection;
        $querySelection = new QuerySelection();
        $querySelection->layerId = 'point';
        $querySelection->useInQuery = true;
        $querySelections[] = $querySelection;
        $queryRequest->querySelections = $querySelections;

        $mapRequest = $this->createRequest();
        $mapRequest->queryRequest = $queryRequest;        
        
        return $mapRequest;
    }

    private function assertQueryResultWithAttributes($queryResult) {

        $this->assertEquals(count($queryResult->tableGroup->tables), 3);
        $this->assertEquals($queryResult->tableGroup->tables[0]->tableId,
                            "polygon");

        $polygonRows = $queryResult->tableGroup->tables[0]->rows; 
        $this->assertEquals(count($polygonRows), 1);
        $this->assertEquals($polygonRows[0]->rowId, '1'); 
        $this->assertEquals($polygonRows[0]->cells, 
                                        array('1', 'Cé bô le françès'));        
    }

    private function assertQueryResultNoAttributes($queryResult) {

        $this->assertEquals(count($queryResult->tableGroup->tables), 3);
        $this->assertEquals($queryResult->tableGroup->tables[0]->tableId,
                            "polygon");

        $polygonRows = $queryResult->tableGroup->tables[0]->rows; 
        $this->assertEquals(count($polygonRows), 1);
        $this->assertEquals($polygonRows[0]->rowId, '1'); 
        $this->assertEquals($polygonRows[0]->cells, array());        
    }

    function testQueryAllLayers($direct = false) {

        $mapRequest = $this->getMapRequestAllLayers();
        $mapResult = $this->getMap($mapRequest);

        $this->assertQueryResultWithAttributes($mapResult->queryResult);

        $this->redoDirect($direct, __METHOD__);
    }

    function testQueryUsingHilight($direct = false) {

        $this->setMapId('test_query_hilight.test');
                
        $mapRequest = $this->getMapRequestAllLayers();
        $mapResult = $this->getMap($mapRequest);

        $this->assertQueryResultWithAttributes($mapResult->queryResult);

        $this->redoDirect($direct, __METHOD__);
    }

    function testQueryWithMask($direct = false) {

        $mapRequest = $this->getMapRequestAllLayers();
        $mapRequest->queryRequest->defaultMaskMode = true;
        
        $mapResult = $this->getMap($mapRequest);

        $this->assertQueryResultWithAttributes($mapResult->queryResult);

        $this->redoDirect($direct, __METHOD__);
    }

    function testQueryNoAttributes($direct = false) {

        $mapRequest = $this->getMapRequestNoAttributes();
        $mapResult = $this->getMap($mapRequest);

        $this->assertQueryResultNoAttributes($mapResult->queryResult);

        $this->redoDirect($direct, __METHOD__);
    }

    function testQueryUseInQuery($direct = true) {

        $mapRequest = $this->getMapRequestUseInQuery();
        $mapResult = $this->getMap($mapRequest);

        $this->assertQueryResultNoAttributes($mapResult->queryResult);

        $this->redoDirect($direct, __METHOD__);
    }

    
}
?>