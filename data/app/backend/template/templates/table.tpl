<div id="result">
    <div style="width: 100%">
        <div class="table_body">
            <table class="text" id="zebra">
                <tr>
                    {foreach from=$tableFields item=field }
                        <th >
                            {if $field.is_sortable}
                                <a onclick="javascript:doSort( '{$field.field}' , '{if $sort.direct=="desc"}asc{else}desc{/if}' );" href="javascript:void(0);">
                                <u>
                            {/if}
                            
                            {$field.name}
                            
                            {if $field.is_sortable}
                               {if $sort}<img src="/backend/img/{$sort.direct}.gif" title="{$sort.direct}">{/if}</u></a> 
                            {/if}
                        </th>
                    {/foreach}
                    <th width="30px">&nbsp;</th>
                </tr>
                {foreach item=item from=$collection}
                <tr>
                    {foreach from=$tableFields item=field  }
                        {assign var=fieldName value=$field.field}
                        <td class="mixed">
                            {if $field.call_method }
                                {$item->$field.call_method()}
                            {elseif $field.method }
                               {printData field=$field entity=$item}
                            {else}
                               {$item->$fieldName}
                            {/if}
                        </td>
                    {/foreach}
                    <td>
                        <A  href="{$fvConfig->get('dir_web_root')}{$path}/edit/?id={$item->getPk()}" 
                            onclick="go('{$fvConfig->get('dir_web_root')}{$path}/edit/?id={$item->getPk()}'); return false;"
                            >
                            <img src="{$fvConfig->get('dir_web_root')}img/edit_icon.png" title="редактировать" width="16" height="16">
                        </a>
                        <a href="javascript: void(0);" 
                            onclick="if (confirm('Вы действительно желаете удалить страницу?')) go('{$fvConfig->get('dir_web_root')}items/delete/?id={$item->getPk()}'); return false;"
                            >
                            <img src="{$fvConfig->get('dir_web_root')}img/delete_icon.png" title="удалить" width="16" height="16">
                        </a>
                    </td>
                </tr>
                {/foreach}
            </table>
        </div>
        {if $collection->hasPaginate()}
            <div id="manager_param_paging" class="paging">
                {$collection->showPagerAjax(false,"doPager")}
            </div>
        {/if}
        <div class="operation">
            <a href="{$fvConfig->get('dir_web_root')}items/edit/" onclick="go('{$fvConfig->get('dir_web_root')}items/edit/'); return false;" class="add">добавить</a>
            <div style="clear: both;"></div>
        </div>
    </div>
    <script language="JavaScript">
        <!--
        {literal}
            jQuery(function($){
                jQuery( "#zebra" ).zebra();
            })
        {/literal}
        //-->
    </script>
</div>
