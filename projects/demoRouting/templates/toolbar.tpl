{strip}
<span id="toolbar">
  {counter start=-1 print=false name=tindex}
  {foreach from=$tools item=tool}
  <img src="{r type=gfx plugin=$tool->plugin}{$tool->id}.gif{/r}" class="toolbar_off" alt="{$tool->id}" title="{t}{$tool->id}{/t}" onclick="CheckRadio('{counter name=tindex}');mainmap.{$tool->id}('map');setActiveButton('{$tool->id}', true);" id="{$tool->id}_icon" 
  
  {if $selected_tool == $tool->id} onload="setActiveButton('{$tool->id}', false);"{/if}
  />&nbsp;
  {/foreach}
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="hidden" name="recenter_none" value="142776,4757216,1083857,5409261" />
  <input type="image" 
    onclick="javascript:document.carto_form.recenter_none.name='recenter_bbox';"
    src="{r type=gfx/layout}fullextent.gif{/r}"
    title="{t}full_extent{/t}" alt="{t}full_extent{/t}" />
  &nbsp;&nbsp;
  <a href="javascript:resetSession();">
    <img align ="middle" src="{r type=gfx/layout}2_recur.png{/r}" alt="{t}reset session{/t}" title="{t}Reset session{/t}" />
  </a>
</span>
&nbsp;&nbsp;
<div style="display:none;">
  {counter start=-1 print=false name=tindex}
  {foreach from=$tools item=tool}
  <input type="radio" name="tool" 
  value="{$tool->id}" 
  onclick="mainmap.{$tool->id}('map');"
  {if $selected_tool == $tool->id}checked="checked"{/if} id="{$tool->id}" />
  {/foreach}
</div>
{/strip}
