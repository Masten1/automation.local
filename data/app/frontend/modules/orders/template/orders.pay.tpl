{if !$order->isNew()}
    <div class="btn-group btn-group-justified">
        <a class="btn btn-primary navlink" href="/orders/edit/{$order->getPk()}">Заявка</a>
        <a class="btn btn-primary navlink" href="/orders/services/{$order->getPk()}">Договор</a>
        <a class="btn btn-primary navlink active" href="/orders/pay/{$order->getPk()}">Платежи</a>
        <a class="btn btn-primary navlink" href="/orders/transfer/{$order->getPk()}">Посадка/Поселение</a>
        <a class="btn btn-primary navlink" href="/orders/ok/{$order->getPk()}">OK!</a>
    </div>
{/if}

<h3 class="underline">Платежи</h3>

<div>
    <span class="order-pay-top">Всего к оплате: <strong class="total">{$order->getPrice()}</strong> грн</span>
    <span class="order-pay-top">Оплачено: <strong class="payment">{$order->getPayment()}</strong> грн</span>
    <span class="order-pay-top">Остаток: <strong class="rest">{$order->getRest()}</strong> грн</span>
</div>

<table class="payments table table-condensed">
    <thead>
    <tr>
        <th class="col-lg-2">Дата</th>
        <th class="col-lg-2">Сумма</th>
        <th class="col-lg-2">Менеджер</th>
        <th class="col-lg-2">Комментарий</th>
        <th class="col-lg-2">Категория</th>
        <th class="col-lg-2">Источник</th>
    </tr>
    </thead>
    <tbody id="paymentBody">
    {foreach from=$order->payments->get() item=pay}
        {$pay->asAdorned()}
    {/foreach}
    </tbody>
</table>
<div class="add-payment">
    <form action="/orders/addpayment">
        <input type="hidden" name="data[orderId]" value="{$order->getPk()}" />
        <div class="pull-left">
            <div class="datepick"></div>
            <div class="form-group">
                <input type="text" class="form-control" name="data[ctime]" id="ctime" value="{$smarty.now|date_format:'%d.%m.%Y %T'}" />
            </div>

        </div>
        <div class="pull-left second-pay">
            <div class="form-group">
                <label for="paySum">Сумма</label>
                <input class="form-control" id="paySum" type="text" name="data[sum]" />
            </div>
            <div class="form-group">
            <label for="offcategory">Категория</label>
                <select class="form-control" name="data[serviceCategoryId]" id="offcategory">
                    <option value="">---</option>
                    {foreach from=$order->offer->getServiceCategories() item=category}
                        <option value="{$category->getPk()}"  >
                            {$category->name->get()}
                        </option>
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label for="sourcePay">Источник платежа</label>
                <select class="form-control" name="data[sourceId]" id="sourcePay">
                    <option value="">---</option>
                    {foreach from=$sources item=source}
                        <option value="{$source->getPk()}">
                            {$source->name->get()}
                        </option>
                    {/foreach}
                </select>
            </div>


            <div class="form-group">
                <label for="payComment">Комментарий</label>
                <textarea class="form-control" name="data[comment]" id="payComment" ></textarea>
            </div>


            <button data-record="#paymentBody" class="btn btn-info save-new pull-right">Сохранить данные</button>
        </div>
    </form>
</div>

{$comment}
<script src="/js/frontend/pay.js"></script>
<h3>История заказа</h3>
{$log}


