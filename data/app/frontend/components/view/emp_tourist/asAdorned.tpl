<em>{$this->entity->name->get()}</em>
{foreach from=$this->entity->phones->get() item=phone}
    <div>{$phone}</div>
{/foreach}