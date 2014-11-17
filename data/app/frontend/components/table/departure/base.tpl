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

    </tbody>
</table>

{if $parentComp->getAllowNew()}
    <div style="margin-bottom: 15px">
        <a class="btn btn-success pull-right navlink" href="/{$parentComp->getModule()}/edit">Новая запись</a>
    </div>
{/if}