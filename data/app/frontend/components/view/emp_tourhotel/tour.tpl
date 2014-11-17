<div id="hotel-container-{$this->entity->getPk()}" data-hotel="{$this->entity->getPk()}" class="hotel-container">
    <strong class="text-ul">{$this->entity->hotel->name}</strong>
    <div><strong>Виды номеров:</strong></div>
    {foreach from=$this->entity->hotel->rooms->get() item=room}
    <div class="rooms">
        <div class="info{if $room->checkBusy($this->entity->getPk())} red{/if}">
            Название: <strong>{$room->type->get()}</strong>
        </div>
        <div class="info">
            Мест: <strong>{$room->quantity->get()}</strong>
        </div>
    </div>
    {/foreach}
    <div class="updating">Занято мест: <strong class="toupdate">{$this->entity->getBusy()}</strong></div>
    <div class="text-center" style="margin-top: 10px">
        <button class="btn btn-danger btn-sm removehotel" data-id="{$this->entity->getPk()}" data-href="/tours/removehotel">Удалить</button>
    </div>
</div>