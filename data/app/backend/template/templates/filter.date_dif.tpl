
<label for="{$fieldName}_from">{$name} начало</label>
<input type="text" name="{$arrayName}[{$fieldName}_from]" value="" id="{$fieldName}_from" readonly="readonly"  class="dateselector">
<img src="{$fvConfig->get('dir_web_root')}img/calendar_delete.png" width="16" height="16" border="0" class="dateselector_clear" title="очистить дату" onclick="$('{$fieldName}_from').value='';">
<div style="clear: both;"></div>

<label for="{$fieldName}_to">{$name} конец</label>
<input type="text" name="{$arrayName}[{$fieldName}_to]" value="" id="{$fieldName}_to" readonly="readonly" class="dateselector">
<img src="{$fvConfig->get('dir_web_root')}img/calendar_delete.png" width="16" height="16" border="0" class="dateselector_clear" title="очистить дату" onclick="$('{$fieldName}_to').value='';">
<div style="clear: both;"></div>

<br />

<script language="JavaScript">
<!--
  {literal}
    
    jQuery(function($){
      
        $("#{/literal}{$fieldName}_from{literal}" ).datetime({});  
        $("#{/literal}{$fieldName}_to{literal}" ).datetime({});  
        
    })
  {/literal}
//-->
</script>