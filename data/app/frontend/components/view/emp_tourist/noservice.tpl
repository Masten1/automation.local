<div data-name="{$this->entity->name->get()}" {if $this->entity->getHotel($tour->getPk())}data-oldhotel="{$this->entity->getHotelPk($tour->getPk())}"{/if} {if $this->entity->getTransport($tour->getPk())}data-old="{$this->entity->getTransportPk($tour->getPk())}"{/if} data-tourist="{$this->entity->getPk()}" class="tourist-block tourist-drag">
    <div class="col-lg-3">
        <strong>{$this->entity->name->get()}</strong>
    </div>
    <div class="col-lg-3">
        {if $this->entity->isGroupLeader($tour)}
            <strong style="text-decoration: underline">Руководитель группы</strong>
        {else}
            {$this->entity->getGroupLeader($tour)}
        {/if}
    </div>
    <div class="col-lg-2">
        {$this->entity->getPhoneString()}
    </div>
    <div class="col-lg-2">
        {$this->entity->getOrderHotel($tour)|default:"Не выбрано"}
    </div>
    <div class="col-lg-2">
        {$this->entity->getOrderTransport($tour)|default:"Не выбрано"}
    </div>
    <div style="clear: both" class="clearfix">
        <div class="col-lg-11">
            <strong>Услуги: </strong>
            {if $this->entity->isGroupLeader($tour)}
                {foreach name=aservices from=$this->entity->getAddServices($tour) item=aService}
                    {$aService->comment->get()}{if !$smarty.foreach.aservices.last}, {/if}
                {/foreach}
            {/if}
            {foreach name=services from=$this->entity->getServices($this->entity->getOrderId($tour), true) item=service}
                <strong>{$service->offservice->service->category}: </strong> {$service->offservice->service->name->get()}{if !$smarty.foreach.services.last}, {/if}
            {/foreach}
        </div>
        <div class="col-lg-1">
            <a class="navlink" href="/orders/edit/{$this->entity->getOrderId($tour)}">Заявка</a>
        </div>
    </div>
    <div style="clear:both">
        <div class="col-lg-4"><strong>Пожелания по проезду:</strong> {$this->entity->getTransportComment($tour)}</div>
        <div class="col-lg-4"><strong>Пожелания по проживанию:</strong> {$this->entity->getHotelComment($tour)}</div>
        <div class="col-lg-4"><strong>Общие пожелания:</strong> {$this->entity->getGeneralComment($tour)}</div>
    </div>
</div>