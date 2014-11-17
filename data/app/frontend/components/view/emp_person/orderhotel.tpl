<form class="form-inline form-service clearfix" action="/orders/addhotel">
    <input type="hidden" name="orderhotel[touristId]" value="{$this->entity->getPk()}" />
    <input type="hidden" name="tourhotel[tourId]" value="{$order->tour->getPk()}"/>
    {assign var="myhotel" value=$this->entity->getOrderHotel($order->tour)}
    {if $myhotel}<input type="hidden" name="orderhotel[id]" value="{$myhotel->getPk()}"/>{/if}
    <div class="col-lg-2">
        <strong>{$this->entity->name->get()}</strong>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control hotel-list" data-room="#rooms-{$this->entity->getPk()}" name="tourhotel[hotelId]">
            <option value="">Выберите отель</option>
            {foreach from=$order->tour->offer->direction->hotels->get() item=hotel}
                <option value="{$hotel->getPk()}" {if $myhotel && $myhotel->tourhotel->hotel->getPk() === $hotel->getPk()}selected{/if} >{$hotel->name->get()}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control" id="rooms-{$this->entity->getPk()}" name="orderhotel[roomId]">
            {if !$myhotel}
                <option value="0">Выберите номер</option>
            {else}
                {foreach from=$myhotel->tourhotel->hotel->rooms->get() item=room}
                    <option value="{$room->getPk()}" {if $myhotel->room->getPk() === $room->getPk()}selected{/if} >{$room} - {if $room->checkBusy($myhotel->thId->get())}Занято{else}Свободно{/if}</option>
                {/foreach}
            {/if}

        </select>
    </div>

    <div class="col-lg-3 form-group">
        <input class="form-control" value="{if $myhotel}{$myhotel->getComment($order)}{else}{$order->hotelWish->get()}{/if}" type="text" name="orderhotel[comment]" id="service-comment-{$this->entity->getPk()}" />
    </div>
    <div class="form-group nosize action"><a href="/orders/addhotel" class="glyphicon glyphicon-check green save-new"></a></div>
</form>