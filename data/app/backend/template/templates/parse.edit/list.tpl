<label>
    {$field->getName()}:
    <select name='{$arrayName}[{$name}]' id='{$name}'>
        {if $field->getList($entity)}
            {foreach from=$field->getList($entity) key=key item=item}
                <option value="{$key}" {if $key == $field->get() || $key == $defaultList}selected{/if}>{$item}</option>
            {/foreach}
        {/if}
    </select>
</label>
<br />