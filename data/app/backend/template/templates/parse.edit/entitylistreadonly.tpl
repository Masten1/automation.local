<label for="">{$field->getName()}</label>

<div>
    {foreach from=$field->getForeigns() item=item}
        {if $item|instanceof:iWidget}
            {$item->widget()}
        {else}
            {$item}
        {/if}
    {/foreach}
</div>