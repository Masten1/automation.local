<form action='/{$path}/activate/'>
    <input type='hidden' name='id' value='{$entity->getPk()}'>
    <input type='checkbox' name='m[{$fieldName}' class='activator ui-button'  { if $entity->get($fieldName) } checked="checked" {/if} >
</form>