    <div style="width: 100%">

        {if $collection->hasPaginate()}
            <div id="manager_param_paging" class="paging">
                {$collection->showPagerAjax(false,"doPager")}
            </div>
        {/if}

        {if $create}
            <div class="operation">
                <a href="{$fvConfig->get('dir_web_root')}{$path}/edit/" onclick="go('{$fvConfig->get('dir_web_root')}{$path}/edit/'); return false;" class="add">добавить</a>
                <div style="clear: both;"></div>
            </div>
        {/if}

        <div class="table_body">
            <table class="text" id="zebra">
                <tr>
                {foreach from=$entityFields key=name item=field }
                    {if $field->isListable()}
                        <th>
                            {if $field->isSortable()}
                                <a href="#{$fvConfig->get('dir_web_root')}{$smarty.request.__url}?{$queryString}&sort={$name}&order={if $smarty.request.order==1}0{else}1{/if}">{$field->getName()} {if $smarty.request.sort == $name}<img src="/backend/img/{if $smarty.request.order}asc{else}desc{/if}.gif"/>{/if}</a>
                                {else}
                                {$field->getName()}
                            {/if}
                        </th>
                    {/if}
                {/foreach}
                    <th width="75px">&nbsp;</th>
                </tr>
            {foreach item=item from=$collection}
                <tr >
                    {foreach from=$entityFields key=fieldName item=fieldType }
                    {assign var=field value=$item->getField($fieldName)}

                        {if $field->isListable()}
                            <td class="mixed">
                                {$item->getFieldAdorned($fieldName)}
                            </td>
                        {/if}
                    {/foreach}
                    <td style="align: center;">
                        {if $edit}
                        <a  href="{$fvConfig->get('dir_web_root')}{$path}/edit/?id={$item->getPk()}"
                            onclick="go('{$fvConfig->get('dir_web_root')}{$path}/edit/?id={$item->getPk()}'); return false;"
                                >
                            <img src="{$fvConfig->get('dir_web_root')}img/edit_icon.png" title="редактировать" width="16" height="16">
                        </a>
                        {/if}

                        {if $delete}
                        <a href="javascript: void(0);"
                           onclick="if (confirm('Вы действительно желаете удалить страницу?')) go('{$fvConfig->get('dir_web_root')}{$path}/delete/?id={$item->getPk()}'); return false;"
                                >
                            <img src="{$fvConfig->get('dir_web_root')}img/delete_icon.png" title="удалить" width="16" height="16">
                        </a>
                        {/if}

                        {if $statistics}
                            <a  href="{$fvConfig->get('dir_web_root')}{$path}/statistics/?id={$item->getPk()}"
                                onclick="go('{$fvConfig->get('dir_web_root')}{$path}/statistics/?id={$item->getPk()}'); return false;"
                                    >
                                <img src="{$fvConfig->get('dir_web_root')}img/menu_icons/vote.gif" title="Статистика" width="16" height="16">
                            </a>
                        {/if}
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
        {if $create}
        <div class="operation">
            <a href="{$fvConfig->get('dir_web_root')}{$path}/edit/" onclick="go('{$fvConfig->get('dir_web_root')}{$path}/edit/'); return false;" class="add">добавить</a>
            <div style="clear: both;"></div>
        </div>
        {/if}
    </div>
