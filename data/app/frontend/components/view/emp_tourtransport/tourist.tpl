<form class="form-inline form-service clearfix" action="/orders/addvehicle" id="vehicle-{$this->entity->getPk()}">
    <input type="hidden" name="data[touristId]" value="{$this->entity->tourist->getPk()}" />
    <input type="hidden" name="data[orderId]" value="{$order->getPk()}" />
    <input type="hidden" name="data[id]" value="{$this->entity->getPk()}" />
    <div class="col-lg-2">
        <strong>{$this->entity->tourist->name->get()}</strong>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control vehicle-list" data-room="#rooms-{$this->entity->getPk()}" name="data[vehicleId]">
            <option value="">Выберите автобус</option>
            {foreach from=$vehicles item=vehicle}
                <option value="{$vehicle->getPk()}" {if $this->entity->vehicle->getPk() === $vehicle->getPk()}selected{/if} >{$vehicle->model->get()}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-lg-3 form-group">
        <select class="form-control" name="data[place]">
            {foreach from=$this->entity->getPlaceList() item=place key=key}
                <option value="{$key}" {if $this->entity->place eq $key}selected{/if} >{$place}</option>
            {/foreach}
        </select>
    </div>

    <div class="col-lg-3 form-group">
        <input class="form-control" value="{$this->entity->comment->get()}" type="text" name="data[comment]" id="service-comment-{$this->entity->getPk()}" />
    </div>
    <div class="form-group nosize action"><a href="/orders/addvehicle" class="glyphicon glyphicon-check green save-new"></a></div>
</form>