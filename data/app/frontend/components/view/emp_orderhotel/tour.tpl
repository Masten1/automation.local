<div id="hotel-container-{$this->entity->hotel->getPk()}" data-hotel="{$this->entity->hotel->getPk()}" class="hotel-container">
    <strong class="text-ul">{$this->entity->hotel->name}</strong>
    <div><strong>Виды номеров:</strong></div>
    {foreach from=$this->entity->hotel->rooms->get() item=room}
    <div class="rooms">
        <div class="info">
            Название: <strong>{$room->type->get()}</strong>
        </div>
        <div class="info">
            Мест: <strong>{$room->quantity->get()}</strong>
        </div>
    </div>
    {/foreach}
    <div class="updating">Занято мест: <strong class="toupdate">{$this->entity->order->tour->getBusy("Emp_OrderHotel", "hotelId", $this->entity->hotel->getPk())}</strong></div>
</div>