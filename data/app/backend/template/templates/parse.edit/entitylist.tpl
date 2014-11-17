<label>{$field->getName()}:</label>
<div style="margin-left: 10px; margin-bottom: 10px;">
    {foreach from=$field->getForeigns() item=foreign}
        <div style="display: inline-block;">
            <input type="checkbox" name="{$arrayName}[{$name}][]" value="{$foreign->getPk()}" id="foreign-{$field->getKey()}-{$foreign->getPk()}" {if $field->isAssigned($foreign->getPk())}checked="checked"{/if}>
            <label for="foreign-{$field->getKey()}-{$foreign->getPk()}">{$foreign}</label>
        </div>
    {/foreach}
</div>