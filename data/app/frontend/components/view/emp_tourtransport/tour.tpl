<div id="vehicle-container-{$this->entity->getPk()}" data-transport="{$this->entity->getPk()}" class="transport-container">
    <a class="help-block text-center" href="/tours/list/{$this->entity->getPk()}">Путевой лист</a>

    <strong class="text-ul">{$this->entity->vehicle->model}</strong><br />
    <strong class="text-ul">{$this->entity->vehicle->number}</strong>
    <div>
        {assign var=dField value=$this->entity->getField("driver")}
        <label for="driver-{$this->entity->getPk()}">Водитель:</label>
        <select name="driver" class="transport-driver" id="driver-{$this->entity->getPk()}">
            {foreach from=$dField->getList($this->entity) key="dKey" item="dValue"}
                <option {if $this->entity->driverId->get() == $dKey}selected{/if} value="{$dKey}">{$dValue}</option>
            {/foreach}
        </select>
        <strong id="driverPhone" class="text-ul">{if $this->entity->driverId->get()}{$this->entity->driver->phone->get()}{/if}</strong>
    </div>
    {foreach from=$this->entity->vehicle->seats->get() item=seat}
        <div class="rooms">
            <div class="info{if $seat->checkBusy($this->entity->getPk())} red{/if}">
                Название: <strong>{$seat->type->get()}</strong>
            </div>
        </div>
    {/foreach}
    <div class="info">Всего мест: <strong>{$this->entity->vehicle->getTotalSeats()}</strong></div>
    <div class="updating">Занято мест: <strong class="toupdate">{$this->entity->getBusy()}</strong></div>
    <div class="freeall">Свободно мест: <strong class="free">{$this->entity->getFree()}</strong></div>
    <div class="route">
        <strong class="text-ul">
            Маршрут:
            {$this->entity->getRoute()}
        </strong>
    </div>

    <div class="text-center" style="margin-top: 10px">
        <button class="btn btn-danger btn-sm removeitem" data-id="{$this->entity->getPk()}" data-href="/tours/removetransport">Удалить</button>
    </div>
</div>