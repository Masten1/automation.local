<label>{$field->getName()}: </label>

<script>
{literal}
    var foreignElementTemplate = new Template(
        '<div id="foreign-element-container-#{fieldName}-#{value}" class="selectedList">'+
            '#{label}'+
            '<a onclick="window.entityList#{fieldName}.removeElement(#{value}); return false;" href="javascript: void(0);">'+
                '<img height="16" width="16" title="удалить" src="/backend/img/delete.png" />'+
            '</a>'+
            '<input type="hidden" name="#{arrayName}[#{fieldName}][]" value="#{value}" />'+
        '</div>'
          );
{/literal}
{if $field->isFromJSON()}
    var source{$field->getKey()} = '{$fvConfig->get('dir_web_root')}{$path}/getForeign/?id={$field->getPK()}&references={$field->getKey()}';
{else}
    source{$field->getKey()} = new Array (
    {foreach from=$field->getForeigns() item=foreign name=foreign}
        {literal}{{/literal}
        value: '{$foreign->getPk()}',
        label: '{$foreign|escape:'html'}',
        checked: {if $field->isAssigned($foreign->getPk())}true{else}false{/if}
        {literal}}{/literal}
        {if !$smarty.foreach.foreign.last},{/if}
    {/foreach}
    );
{/if}

window.entityList{$field->getKey()}= new entityList('{$field->getKey()}', '{$arrayName}', source{$field->getKey()}, foreignElementTemplate);

</script>

<input type="text" id="foreign-input-{$field->getKey()}" name="foreign-input-{$field->getKey()}" style="margin-bottom: 0px;"/>
<p style="color: #666; font-size: 10px; font-style: italic;"> — Начните вводить название

</p>

<div id="foreign-container-{$field->getKey()}" class="entity-wrapper">
</div>

