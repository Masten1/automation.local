{if !$order->isNew()}
    <div class="btn-group btn-group-justified">
        <a class="btn btn-primary navlink" href="/orders/edit/{$order->getPk()}">Заявка</a>
        <a class="btn btn-primary navlink" href="/orders/services/{$order->getPk()}">Договор</a>
        <a class="btn btn-primary navlink" href="/orders/pay/{$order->getPk()}">Платежи</a>
        <a class="btn btn-primary navlink active" href="/orders/transfer/{$order->getPk()}">Посадка/Поселение</a>
        <a class="btn btn-primary navlink" href="/orders/ok/{$order->getPk()}">OK!</a>
    </div>
{/if}

<div style="margin-top: 15px" class="form-group col-lg-7">
    <label for="hotelSelect" class="col-lg-3">Поселение</label>
    <div class="col-lg-9">
        <select class="form-control default-selector" data-link=".hotel-list" id="hotelSelect">
                <option value="">Выберите обьект - применяется ко всем</option>
            {foreach from=$order->offer->direction->hotels->get() item=hotel}
                <option value="{$hotel->getPk()}">{$hotel->name->get()}</option>
            {/foreach}
        </select>
    </div>
</div>

<div style="clear:both">
    {foreach from=$order->tourists->get() item=tourist}
        {$tourist->render('orderhotel')}
    {/foreach}
</div>

<hr>
<div style="margin-top: 15px" class="form-group col-lg-7">
    <label for="vehicleSelect" class="col-lg-3">Транспорт</label>
    <div class="col-lg-9">
        <select class="form-control default-selector" data-link=".vehicle-list" id="vehicleSelect">
            <option value="">Выберите обьект - применяется ко всем</option>
            {foreach from=$vehicles item=vehicle}
                <option value="{$vehicle->getPk()}">{$vehicle->model->get()}</option>
            {/foreach}
        </select>
    </div>
</div>

<div style="clear:both">
    {foreach from=$order->tourists->get() item=tourist}
        {$tourist->render('ordervehicle')}
    {/foreach}
</div>

{$comment}


<script src="/js/frontend/transfer.js"></script>
<h3>История заказа</h3>
{$log}