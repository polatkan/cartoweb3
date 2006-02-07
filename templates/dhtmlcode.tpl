

<link rel="stylesheet" type="text/css" href="{r type=css}dhtml.css{/r}" />
<script type="text/javascript" src="{r type=js}x_cartoweb.js{/r}"></script>
<script type="text/javascript" src="{r type=js}wz_jsgraphics.js{/r}"></script>
<script type="text/javascript" src="{r type=js}Logger.js{/r}"></script>
<script type="text/javascript" src="{r type=js}dhtmlAPI.js{/r}"></script>
<script type="text/javascript" src="{r type=js}dhtmlFeatures.js{/r}"></script>
<script type="text/javascript" src="{r type=js}dhtmlInit.js{/r}"></script>
{if $edit_allowed|default:''}<script type="text/javascript" src="{r type=js plugin=edit}dhtmlEdit.js{/r}"></script>{/if}
<script type="text/javascript" src="{r type=js}folders.js{/r}"></script>
<script type="text/javascript">
/*<![CDATA[*/
// TODO application object
// alert messages to be translated
_m_overlap = "{t}overlapping polygons are not allowed{/t}"
_m_delete_feature = "{t}Are you sure ?{/t}";
_m_bad_object = "{t}Not conform object{/t}";



{literal}
function initMap() {{/literal}
    mainmap.setExtent({$bboxMinX},{$bboxMinY},{$bboxMaxX},{$bboxMaxY});
    factor = {$factor};{literal}

    var rasterLayer = new Layer("raster");{/literal}
    var feature = new Raster('{$mainmap_path}');{literal}
    rasterLayer.addFeature(feature);
    mainmap.addLayer(mainmap,rasterLayer);

    var drawLayer = new Layer("drawing");{/literal}
{if $edit_allowed|default:''}
{foreach from=$features item=feature}
    var feature = new Feature("{$feature->WKTString}");
    feature.id = "{$feature->id}";
    feature.attributes = new Array({$feature->attributesAsString});
    feature.operation = "{$feature->operation|default:'undefined'}";
    drawLayer.addFeature(feature);  
{/foreach}
{/if} {* end edit allowed *}
    mainmap.addLayer(mainmap,drawLayer);

    mainmap.currentLayer = drawLayer;    
    
{if $edit_allowed|default:''}   
{if $attribute_names|default:''}
    mainmap.editAttributeNames = new Array({$attribute_names});
    mainmap.editAttributeNamesI18n = new Array({$attribute_names_i18n});
{/if}
{if $attribute_types|default:''}
    mainmap.editAttributeTypes = new Array({$attribute_types});    
{/if}
{if $attribute_names|default:''}
    mainmap.drawEditAttributesTable();
{/if}
//    mainmap.handleEditTable();
    insert_feature_max_num = {$edit_max_insert};
{/if} {* end edit allowed *}
{literal}
}{/literal}
/*]]>*/
</script>
