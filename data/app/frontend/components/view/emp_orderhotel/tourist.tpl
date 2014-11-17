<form class="form-inline form-service clearfix" action="/orders/addhotel" id="hotel-{$this->entity->getPk()}">
    <input type="hidden" name="data[touristId]" value="{$this->entity->tourist->getPk()}" />
    <input type="hidden" name="data[orderId]" value="{$order->getPk()}" />
    <input type="hidden" name="data[id]" value="{$this->entity->getPk()}" />
    <div class="col-lg-2">
        <strong>{$this->entity->tourist->name->get()}</strong>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control hotel-list" data-room="#rooms-{$this->entity->getPk()}" name="data[hotelId]">
            <option value="">Выберите отель</option>
            {foreach from=$order->tour->offer->direction->hotels->get() item=hotel}
                <option value="{$hotel->getPk()}" {if $this->entity->hotel->getPk() === $hotel->getPk()}selected{/if} >{$hotel->name->get()}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control" id="rooms-{$this->entity->getPk()}" name="data[roomId]">
            {if !$this->entity->hotel}
                <option value="">Выберите номер</option>
            {else}
                {foreach from=$this->entity->hotel->rooms->get() item=room}
                    <option value="{$room->getPk()}" {if $this->entity->room->getPk() === $room->getPk()}selected{/if} >{$room}</option>
                {/foreach}
            {/if}

        </select>
    </div>

    <div class="col-lg-3 form-group">
        <input class="form-control" value="{$this->entity->getHotelComment()}" type="text" name="data[comment]" id="service-comment-{$this->entity->getPk()}" />
    </div>
    <div class="form-group nosize action"><a href="/orders/addhotel" class="glyphicon glyphicon-check green save-new"></a></div>
</form>