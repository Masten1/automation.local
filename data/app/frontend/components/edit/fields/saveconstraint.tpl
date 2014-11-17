{if !$entity->isNew()}
<div class="constraintblock">
    <label class="title">{$field->getname()}</label>
    <div class="form-group">
        <div class="col-lg-12" id="add-{$fieldName}">
            {foreach from=$field->get() item=value}
                {$value->asAdornedEdit()}
            {/foreach}
        </div>
    </div>
    {assign var=newConst value=$field->generateForeignEntity()}
    <div class="form-group">
        <label class="col-lg-4 text-right">Новая запись</label>
        <div class="col-lg-8">
            {foreach from=$newConst->getFields() item=const key=cfName}
                {if $const->isEditable()}
                    <div id="const-{$cfName}" class="input-group">
                        <input class="form-control" type="text" name="constraint[{$cfName}]">
                        <input type="hidden" name="constraint[{$field->getForeignEntityKey()}]" value="{$entity->getPk()}" />
                        <a data-record="#add-{$fieldName}" data-fieldname="#const-{$cfName}" class="input-group-addon btn btn-success savelink" href="/save/constraint/{$field->getEntity()}">
                            <span class="glyphicon glyphicon-plus-sign"></span>
                        </a>
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
</div>
{/if}

