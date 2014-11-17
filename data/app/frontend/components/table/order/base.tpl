<h3>{$this->getTitle()}</h3>

{if $this->getAllowNew()}
    <div class="clearfix" style="margin-bottom: 15px">
        <a class="btn btn-success pull-right" href="/{$this->getModule()}/edit">Новая запись</a>
    </div>
{/if}

<div style="margin-bottom: 15px">
    <span>Сумма к оплате: <strong>{$total.sum}</strong>; Оплачено: <strong>{$total.paid}</strong>; Остаток к оплате: <strong>{$total.rest}</strong></span>
</div>
<table class="table table-bordered table-hover table-list">
    <thead>
    <tr>
        <th>#</th>
        {foreach from=$this->entity->getFields() item=field}
            {if $field->isListable()}
                <th>{$field->getName()}</th>
            {/if}
        {/foreach}
        <th>К-во</th>
        <th>К оплате</th>
        <th>Оплачено</th>
        <th>&nbsp</th>
    </tr>
    </thead>
    <tbody>
    {assign var=parentComp value=$this}
    {foreach from=$this->getList() item=record}
            <tr id="record-{$record->getPk()}">
                <td>{$record->getPk()}</td>
                {foreach from=$record->getFields() key=eName item=eField}
                    {if $eField->isListable()}
                        <td>{$record->getFieldAdorned($eName)}</td>
                    {/if}
                {/foreach}
                <td>
                    {$record->getTotalTourists()}
                </td>
                <td>{$record->getPrice()}</td>
                <td>{$record->getPayment()}</td>
                <td class="actions">
                    <a class="glyphicon glyphicon-edit" href="/{$parentComp->getModule()}/edit/{$record->getPk()}"></a>
                    <a data-record="#record-{$record->getPk()}" href="/delete/{$parentComp->entity->getEntity()}/{$record->getPk()}" class="glyphicon glyphicon-remove red msglink confirm"></a>
                </td>
            </tr>

    {/foreach}
    </tbody>
</table>

{if $parentComp->getAllowNew()}
    <div style="margin-bottom: 15px">
        <a class="btn btn-success pull-right" href="/{$parentComp->getModule()}/edit">Новая запись</a>
    </div>
{/if}