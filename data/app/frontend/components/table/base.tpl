<h3>{$this->getTitle()}</h3>

{if $this->getAllowNew()}
    <div class="clearfix" style="margin-bottom: 15px">
        <a class="btn btn-success pull-right navlink" href="/{$this->getModule()}/edit">Новая запись</a>
    </div>
{/if}

<table class="table table-bordered table-hover table-list">
    <thead>
        <tr>
            {foreach from=$this->entity->getFields() item=field}
                {if $field->isListable()}
                    <th>{$field->getName()}</th>
                {/if}
            {/foreach}
            {if $this->isDeletable() || $this->isEditable()}
            <th>&nbsp</th>
            {/if}
        </tr>
    </thead>
    <tbody>
        {foreach from=$this->getList() item=record}
            <tr id="record-{$record->getPk()}">
                {foreach from=$record->getFields() key=ename item=efield}
                    {if $efield->isListable()}
                        <td>{$record->getFieldAdorned($ename)}</td>
                    {/if}
                {/foreach}
                {if $this->isDeletable() || $this->isEditable()}
                <td class="actions">
                    {if $this->isEditable()}
                        <a class="glyphicon glyphicon-edit navlink" href="/{$this->getModule()}/edit/{$record->getPk()}"></a>
                    {/if}
                    {if $this->isDeletable()}
                        <a data-record="#record-{$record->getPk()}" href="/delete/{$this->entity->getEntity()}/{$record->getPk()}" class="glyphicon glyphicon-remove red msglink confirm"></a>
                    {/if}
                </td>
                {/if}
            </tr>
        {/foreach}
    </tbody>
</table>
{if $this->getAllowNew()}
<a class="btn btn-success pull-right navlink" href="/{$this->getModule()}/edit">Новая запись</a>
{/if}
