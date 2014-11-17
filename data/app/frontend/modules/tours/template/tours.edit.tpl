<h3 class="underline col-lg-12">Тур</h3>
<div class="clearfix">
    <div class="col-lg-4">Дата выезда: <strong>{$tour->date->asAdorned()}</strong></div>
    <div class="col-lg-4">Направление: <strong>{$tour->offer->direction}</strong></div>
    <div class="col-lg-4">Длительность: <strong>{$tour->offer->getDuration()}</strong></div>
</div>

<div class="col-lg-12">
    <a href="/tours/hotelfinance/{$tour->getPk()}">Расчётный лист</a>
</div>

<div class="clearfix" style="margin-top: 15px;">
    <div class="col-lg-12">
        <div class="input-group">
            <input placeholder="Комментарий" name="tourCommentInput" id="tourCommentInput" type="text" class="form-control">
            <input type="hidden" id="tourId" value="{$tour->getPk()}"/>
            <span class="input-group-btn">
                <button class="btn btn-info" id="tourComment" type="button">Обновить</button>
            </span>
        </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
</div>

<div id="tourMenu" class="btn-group widget-labels">
    <div data-hide="#tourHotels" data-show="#tourTransport" class="btn btn-primary active">Транспорт</div>
    <div data-hide="#tourTransport" data-show="#tourHotels" class="btn btn-primary">Поселение</div>
</div>

<div id="tourTransport" class="widget-block clearfix">
    <div class="col-lg-3">
        <div id="availableTransports">
            {foreach from=$tour->transports->get() item="transport"}
                {$transport->render("tour")}
            {/foreach}
        </div>
        <div class="form-group addnew">
            <label for="addTransport">Добавить транспорт</label>
            <select class="form-control" id="addTransport">
                <option value="">Выберите автобус</option>
                {foreach from=$tour->getFreeVehicles() item=ex}
                    <option value="{$ex->getPk()}">{$ex->model->get()}</option>
                {/foreach}
            </select>
            <button data-id="{$tour->getPk()}" id="doAddVehicle" data-href="/tours/addnewvehicle" class="btn btn-success">Добавить</button>
        </div>
    </div>

    <div class="col-lg-9">
        <h4>Туристы</h4>
        <div data-vehicle="" id="vehicle-empty" class="vehicle-droppable">
            <strong>Нераспределённые</strong>
            <div class="tourlist-head">
                <div class="col-lg-3">ФИО</div>
                <div class="col-lg-3">Руководитель</div>
                <div class="col-lg-2">Телефон</div>
                <div class="col-lg-2">Где живет</div>
                <div class="col-lg-2">Чем едет</div>
            </div>

            {foreach from=$tour->getNoTransportTourists() item=tourist}
                {$tourist->render("noservice")}
            {/foreach}

        </div>

        <div id="allVehicles">
            {foreach from=$tour->transports->get() item=transport}
                {$transport->render("tourlist")}
            {/foreach}
        </div>

    </div>
</div>

<div id="tourHotels" class="widget-block hide clearfix">
    <div class="col-lg-3">
        <div id="availableHotels">
            {foreach from=$tour->hotels->get() item="hotel"}
                {$hotel->render("tour")}
            {/foreach}
        </div>
        <div class="form-group addnew">
            <label for="addHotel">Добавить отель</label>
            <select class="form-control" id="addHotel">
                <option value="">Выберите отель</option>
                {foreach from=$tour->getFreeHotels() item=ex}
                    <option value="{$ex->getPk()}">{$ex->name->get()}</option>
                {/foreach}
            </select>
            <button data-id="{$tour->getPk()}" id="doAddHotel" data-href="/tours/addnewhotel" class="btn btn-success">Добавить</button>
        </div>
    </div>

    <div class="col-lg-9">
        <h4>Туристы</h4>
        <div data-hotel="" id="hotel-empty" class="hotel-droppable">
            <strong>Нераспределённые</strong>
            <div class="tourlist-head">
                <div class="col-lg-3">ФИО</div>
                <div class="col-lg-3">Руководитель</div>
                <div class="col-lg-2">Телефон</div>
                <div class="col-lg-2">Где живет</div>
                <div class="col-lg-2">Чем едет</div>
            </div>

            {foreach from=$tour->getNoHotelTourists() item=tourist}
                {$tourist->render("noservice")}
            {/foreach}

        </div>

        <div id="allHotels">
            {foreach from=$tour->hotels->get() item=hotel}
                {$hotel->render("tourlist")}
            {/foreach}
        </div>

    </div>
</div>


<h3 class="underline col-lg-12">Все туристы</h3>
{foreach from=$tour->orders->get() item=order}
    <div style="margin-bottom: 5px">Руководитель группы: <strong>{$order->getMainTourist()}</strong></div>
    {if $order->hotelWish->get()}
        <div><strong>Пожелания по проживанию: </strong>{$order->hotelWish->get()}</div>
    {/if}
    {if $order->transportWish->get()}
        <div><strong>Пожелания по проезду: </strong>{$order->transportWish->get()}</div>
    {/if}
    {if $order->generalWish->get()}
        <div><strong>Пожелания по проживанию: </strong>{$order->generalWish->get()}</div>
    {/if}
    <div class="tourlist-head">
        <div class="col-lg-2">ФИО</div>
        <div class="col-lg-2">Телефон</div>
        <div class="col-lg-2">Где живет</div>
        <div class="col-lg-2">Чем едет</div>
        <div class="col-lg-3">Доп.услуги</div>
    </div>
    {foreach from=$order->getTourists() item=tourist}
        {$tourist->render('general')}
    {/foreach}

{/foreach}

<h3 class="underline col-lg-12">Страховка</h3>
{foreach from=$tour->getInsurances() item=insurance}
    <div>
        <a href="/tours/insurance/?id={$insurance->getPk()}" data-id="{$insurance->offservice->getPk()}">{$insurance->offservice->service->name->get()} - {$insurance->getInsuranceCount()}</a>
    </div>
{/foreach}

{$comment}

<h3>История заказов тура</h3>
<table class="table table-bordered table-hover table-list">
    <thead>
    <tr>
        <th>Сообщение</th>
        <th>Время</th>
        <th>Заказ</th>
        <th>Менеджер</th>
    </tr>
    </thead>
    <tbody>
        {foreach from=$tourLog item=record}
            <tr>
                <td>{$record->comment->get()}</td>
                <td>{$record->ctime->asAdorned()}</td>
                <td>{$record->order}</td>
                <td>{$record->manager}</td>
            </tr>
        {/foreach}
    </tbody>
</table>
<script src="/js/frontend/tour.js"></script>