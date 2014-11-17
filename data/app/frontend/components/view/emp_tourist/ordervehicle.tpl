<form class="form-inline form-service clearfix" action="/orders/addvehicle" id="vehicle-{$this->entity->getPk()}">
    <input type="hidden" name="ordertransport[touristId]" value="{$this->entity->getPk()}" />
    <input type="hidden" name="tourtransport[tourId]" value="{$order->tour->getPk()}"/>
    {assign var="mytrans" value=$this->entity->getOrderTransport($order->tour)}
    {if $mytrans}<input type="hidden" name="ordertransport[id]" value="{$mytrans->getPk()}"/>{/if}
    <div class="col-lg-2">
        <strong>{$this->entity->name->get()}</strong>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control vehicle-list" data-room="#seat-{$this->entity->getPk()}" name="tourtransport[vehicleId]">
            <option value="0">Выберите автобус</option>
            {foreach from=$vehicles item=vehicle}
                <option value="{$vehicle->getPk()}" {if $mytrans && $mytrans->transport->vehicle->getPk() === $vehicle->getPk()}selected{/if} >{$vehicle->model->get()}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control" id="seat-{$this->entity->getPk()}" name="ordertransport[seatId]">
            <option value="0">Выберите место</option>
            {if $mytrans}
                {foreach from=$mytrans->transport->vehicle->seats->get() item=seat}
                    <option value="{$seat->getPk()}" {if $mytrans->seat->getPk() === $seat->getPk()}selected{/if} >{$seat} - {if $seat->checkBusy($mytrans->ttId->get())}Занято{else}Свободно{/if}</option>
                {/foreach}
            {/if}
        </select>
    </div>

    <div class="col-lg-3 form-group">
        <input class="form-control" value="{if $mytrans}{$mytrans->getComment($order)}{else}{$order->transportWish->get()}{/if}" type="text" name="ordertransport[comment]" id="service-comment-{$this->entity->getPk()}" />
    </div>
    <div class="form-group nosize action"><a href="/orders/addvehicle" class="glyphicon glyphicon-check green save-new"></a></div>
</form>