<div class="tourist-block">
    <div class="col-lg-2">
        <strong>{$this->entity->name->get()}</strong>
    </div>
    <div class="col-lg-2">
        {$this->entity->getPhoneString()}
    </div>
    <div class="col-lg-2" id="hotel-for-{$this->entity->getPk()}">
        {$this->entity->getOrderHotel($tour)|default:"Не выбрано"}
    </div>
    <div class="col-lg-2" id="transport-for-{$this->entity->getPk()}">
        {$this->entity->getOrderTransport($tour)|default:"Не выбрано"}
    </div>
    <div class="col-lg-3">
        {if $this->entity->isGroupLeader($tour)}
            {foreach name=aservices from=$this->entity->getAddServices($tour) item=aService}
                {$aService->comment->get()}{if !$smarty.foreach.aservices.last}, {/if}
            {/foreach}
        {/if}
        {foreach name=services from=$this->entity->getServices($this->entity->getOrderId($tour), true) item=service}
            {$service->offservice->service->name->get()}{if !$smarty.foreach.services.last}, {/if}
        {/foreach}
    </div>
    <div class="col-lg-1">
        <a class="navlink" href="/orders/edit/{$this->entity->getOrderId($tour)}">Заявка</a>
    </div>
</div>