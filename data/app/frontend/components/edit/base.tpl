<h3 class="text-center">{$this->getTitle()}</h3>
<form class="form-horizontal form-edit" action="{$this->getAction()}" method="POST">
    {foreach from=$this->entity->getFields() item=field key=fieldName}
        {if $field->isEditable()}
            <div>
                {include file='fields/'|cat:$field->getEditMethod()|cat:".tpl" dataName="data" entity=$this->entity}
            </div>
        {/if}
    {/foreach}
    <div class="form-group">
        <input type="hidden" name="data[id]" value="{$this->entity->getPk()}" />
        <div class="container">
            <button type="submit" class="btn btn-primary submit" data-redirect="0" data-href="/{$this->module}/save/{$this->entity->getPk()}">Сохранить</button>
            <button type="submit" class="btn btn-primary submit" data-redirect="1" data-href="/{$this->module}/save/{$this->entity->getPk()}" >Сохранить и выйти</button>
        </div>
    </div>
</form>
<script src="/js/frontend/edit.js"></script>