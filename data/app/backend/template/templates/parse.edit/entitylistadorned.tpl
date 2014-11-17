{if !$isPopup && !$subject->isNew()}
    <label>{$field->getName()}:</label>
    <div id="winResult" style="margin: 15px 0 5px">
        {foreach from=$field->getForeigns() item=foreign}
            - {$foreign->asAdorned()}
        {/foreach}
    </div>

    {if !$field->entityModule}

    <div class="operation-edit">
        <a id="add_new" href="javascript:void(0)" entity="{$field->getEntity()}" default="{$subject->getPk()}" class="add add_new">добавить</a>
        <div style="clear: both;"></div>
    </div>
    {else}

    <div class="operation-edit">
        <a href="{$fvConfig->get('dir_web_root')}{$field->entityModule}/edit/" onclick="go('{$fvConfig->get('dir_web_root')}{$field->entityModule}/edit/?previous={$path}'); return false;" class="add">добавить</a>
        <div style="clear: both;"></div>
    </div>

    {/if}


{/if}