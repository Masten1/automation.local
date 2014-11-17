{foreach from=$fields key=name item=field}
    {if $field->isReadonly()}
        <label>
            {$field->getName()}:<br>
            <div class="readonly">{$entity->getFieldAdorned($name)}</div>
        </label>
        <br />
    {elseif $field->isEditable()}
        {include 
            file='parse.edit/'|cat:$field->getEditMethod()|cat:".tpl" 
            field=$field 
            name=$name
            arrayName='data'|cat:"["|cat:$section|cat:"]"}
    {/if}
{/foreach}