<h3>{$this->getTitle()}</h3>

{if $this->getAllowNew()}
    <div class="clearfix" style="margin-bottom: 15px">
        <a class="btn btn-success pull-right navlink" href="/{$this->getModule()}/edit">Новая запись</a>
    </div>
{/if}

<table class="table table-bordered table-hover table-list">
    <thead>
    <tr>
        <th>Дата отправки</th>
        <th>Количество пассажиров</th>
        <th>Стоимость</th>
        <th>Оплачено</th>
        <th>Остаток</th>
        <th>&nbsp</th>
    </tr>
    </thead>
    <tbody>
    {assign var=parentComp value=$this}
    {foreach from=$this->getList() item=record}
        {if $record->getTotalTourists()}
            <tr>
                <td>{$record->getDate()}</td>
                <td>{$record->getTotalTourists()}</td>
                <td>{$record->getPrice()}</td>
                <td>{$record->getPayment()}</td>
                <td>{$record->getRest()}</td>
                <td class="actions">
                    <a class="glyphicon glyphicon-edit navlink" href="/{$parentComp->getModule()}/edit/{$record->getPk()}"></a>
                </td>
            </tr>
        {/if}
    {/foreach}
    </tbody>
</table>

{if $parentComp->getAllowNew()}
    <div style="margin-bottom: 15px">
        <a class="btn btn-success pull-right navlink" href="/{$parentComp->getModule()}/edit">Новая запись</a>
    </div>
{/if}