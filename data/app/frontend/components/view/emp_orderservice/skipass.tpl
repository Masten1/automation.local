<form class="form-inline form-service" action="/orders/addservice" id="service-{$this->entity->getPk()}">
    <input type="hidden" name="data[id]" value="{$this->entity->getPk()}" />
    <input type="hidden" name="data[orderId]" value="{$order->getPk()}"/>
    <div class="service-title">
        <strong>Cкипасс:</strong>
        <span class="name">{$this->entity->offservice->service->duration}</span>
        <span class="price">{$this->entity->getPrice()} грн</span>
    </div>
    <div class="service-price">
        <input type="text" value="{$this->entity->price->get()}" name="data[price]" id="service-price-{$this->entity->getPk()}">
        <label for="service-price-{$this->entity->getPk()}">грн</label>
    </div>
    <div class="service-comment">
        <input value="{$this->entity->comment->get()}" type="text" name="data[comment]" id="service-comment-{$this->entity->getPk()}" />
    </div>
    <div class="form-group nosize action"><a href="/orders/addservice" class="glyphicon glyphicon-check green save-service"></a></div>
    <div class="form-group nosize action"><a data-id="{$this->entity->getPk()}" data-container="#service-{$this->entity->getPk()}" href="/orders/deleteservice" class="glyphicon glyphicon-remove-circle delete-service red"></a></div>
</form>