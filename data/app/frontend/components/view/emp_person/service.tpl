<div class="tourist-accordion">
    <h3 data-id="{$tourist->getPk()}" class="tourist-head tourist-block" >
        {$tourist->name->get()} |
        <small>
            {foreach from=$tourist->getServices($order->getPk()) item=service name=service}
                {$service->offservice->service}{if !$smarty.foreach.service.last},{/if}
            {/foreach}
        </small>
    </h3>
    <div data-id="{$tourist->getPk()}" class="service-list">
        {foreach from=$tourist->getServices($order->getPk()) item=service}
            {$service->asAdorned('default')}
        {/foreach}
    </div>
</div>
