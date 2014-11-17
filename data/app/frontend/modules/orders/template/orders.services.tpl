{if !$order->isNew()}
    <div class="btn-group btn-group-justified">
        <a class="btn btn-primary navlink" href="/orders/edit/{$order->getPk()}">Заявка</a>
        <a class="btn btn-primary navlink active" href="/orders/services/{$order->getPk()}">Договор</a>
        <a class="btn btn-primary navlink" href="/orders/pay/{$order->getPk()}">Платежи</a>
        <a class="btn btn-primary navlink" href="/orders/transfer/{$order->getPk()}">Посадка/Поселение</a>
        <a class="btn btn-primary navlink" href="/orders/ok/{$order->getPk()}">OK!</a>
    </div>
{/if}

<div id="orderContainer">
    <input type="hidden" id="orderId" value="{$order->getPk()}">
    <div id="orderTourists" class="col-lg-8">
        <div class="ui-accordion ui-widget ui-helper-reset">
            <h3 class="tourist-add-all tourist-head ui-accordion-header ui-state-default ui-helper-reset ui-accordion-icons">Добавить всем</h3>
        </div>
        {foreach from=$order->tourists->get() item=tourist}
            {$tourist->render('service')}
        {/foreach}
    </div>

    <div class="col-lg-8">
        <h5><strong>Дополнительные услуги</strong></h5>
        <form action="/orders/addaservice">
            <table id="addServices">
                {foreach from=$order->addservices->get() item=aService}

                    {$aService->asAdorned()}
                {/foreach}
                <tr>
                    <td class="price">
                        <label for="new-add-price">Цена</label>
                        <input type="text" id="new-add-price" name="data[price]">
                    </td>
                    <td class="comment">
                        <label for="new-add-comment">Примечание</label>
                        <input id="new-add-comment" name="data[comment]">
                    </td>
                    <td class="save-add-service-td">
                        <input type="hidden" name="data[orderId]" value="{$order->getPk()}"/>
                        <a href="/orders/addaservice" data-container="#addServices"
                           class="glyphicon glyphicon-plus-sign green save-service"></a>
                    </td>
                </tr>
            </table>
        </form>
        <div id="priceTotal">
            <strong>Всего к оплате:</strong> <span id="priceValue" {if $order->price->get()}class="fixed"{/if}>{$order->getPrice()}</span> грн {if $order->price->get()}(фиксированная цена)
                <a class="btn btn-info" id="recalculateCost" href="javascript:void(0)">Пересчитать</a>{/if}
        </div>
    </div>
</div>

{$comment}

<script type="text/javascript" src="/js/frontend/services.js"></script>
<h3>История заказа</h3>
{$log}