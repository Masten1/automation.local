<p>{$this->message}</p>
{foreach from=$this->tourist->getFields() key=name item=field}
    {if $field->get() && $field->isListable()}
        <div><strong>{$field->getName()}</strong>: {$field}</div>
    {/if}
{/foreach}
