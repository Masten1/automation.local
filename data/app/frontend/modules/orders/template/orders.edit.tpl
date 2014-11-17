{if !$entity->isNew()}
    <div class="btn-group btn-group-justified">
        <a class="btn btn-primary active navlink" href="/orders/edit/{$entity->getPk()}">Заявка</a>
        <a class="btn btn-primary navlink" href="/orders/services/{$entity->getPk()}">Договор</a>
        <a class="btn btn-primary navlink" href="/orders/pay/{$entity->getPk()}">Платежи</a>
        <a class="btn btn-primary navlink" href="/orders/transfer/{$entity->getPk()}">Посадка/Поселение</a>
        <a class="btn btn-primary navlink" href="/orders/ok/{$entity->getPk()}">OK!</a>
    </div>
{/if}
<div id="orderContainer">
    {if $entity->isNew()}
        <h3>Создание заказа</h3>
    {else}
        <h3>Редактирование заказа</h3>
    {/if}
    <div class="clearfix">
        <div class="col-lg-4">Номер заявки: <strong>{$entity->getPk()}</strong></div>
        <div class="col-lg-4">Дата создания: <strong>{$entity->getCtime()}</strong></div>
        <div class="col-lg-4">Менеджер: <strong>{$entity->manager->name->get()}</strong></div>
    </div>
    <form id="orderForm" class="clearfix" action="/orders/save/{$entity->getPk()}" type="POST">
        {if $entity->isNew()}<input type="hidden" name="order[managerId]" value={$fvUser->getPk()}>{/if}
        <div class="col-lg-3 form-group">
            <label for="source">Источник</label>
            <select class="form-control" name="order[sourceId]" id="source">
                <option value="">Выберите источник</option>
                {foreach from=$sources item=source}
                    <option value="{$source->getPk()}" {if $entity->sourceId == $source->getPk()}selected{/if} >
                        {$source->name->get()}</option>
                {/foreach}
            </select>
        </div>
        <div class="col-lg-3 form-group">
            <input type="hidden" name="shipping[orderId]" value={$entity->getPk()}>
            <label for="directions">Направление</label>
            <select class="form-control" name="order[directionId]" id="directions">
                <option value="">---</option>
                {foreach from=$directions item=dir}
                    <option value="{$dir->getPk()}" {if $entity->direction->getPk() == $dir->getPk()}selected{/if} >
                        {$dir->name->get()}
                    </option>
                {/foreach}
            </select>
        </div>

        <div class="col-lg-3 form-group">
            <label for="offers">Длительность</label>
            <select class="form-control" name="order[offerId]" id="offers">
                <option value="">{$offers->duration}</option>
                {foreach from=$offers item=offer}
                    <option value="{$offer->getPk()}" {if $entity->offer->getPk() == $offer->getPk()}selected{/if} >
                        {$offer->duration->get()}</option>
                {/foreach}
            </select>
        </div>

        <div class="col-lg-3 form-group">
            <label for="order-date">Дата</label>
            <div class="input-group">
                <input class="form-control datetime" type="text" id="order-date" name=order[date]
                       value="{$entity->date->asAdorned()}" />
                <span class="input-group-addon glyphicon glyphicon-calendar"></span>
            </div>
        </div>



        <div class="form-group col-lg-12" style="clear: both">

            <button data-redirect="0" class="btn btn-primary maintour">Обновить информацию тура</button>
            <button data-redirect="1" class="btn btn-primary maintour">Обновить и выйти</button>
        </div>
    </form>
    <h3 class="col-lg-12 underline">Туристы</h3>
    <div class="clearfix">
        <div class="control-header larger">ФИО*</div>
        <div class="control-header">Телефон</div>
        <div class="control-header">E-Мейл</div>
        <div class="control-header smaller">Дата рождения</div>
        <div class="control-header smaller">Паспорт</div>
        <div class="control-header smaller">Дата выдачи</div>
        <div class="control-header">Адрес</div>
    </div>
    <div id="touristList">
        <div id="knownTourists">
            {foreach from=$entity->persons->get() item=person}
                {$person->render('form')}
            {/foreach}
        </div>
        <form class="form-inline form-tourist" id="tourist-new" action="/orders/savetourist">
            <div class="form-group form-group-lg"><input id="new-name" class="form-control input-sm" type="text" name=tourist[name] /></div>
            <div class="form-group"><input id="new-phones" class="form-control input-sm" type="text" name=tourist[phone] /></div>
            <div class="form-group"><input id="new-email" class="form-control input-sm" type="text" name=tourist[email]  /></div>
            <div class="form-group form-group-sm"><input id="new-birth" class="form-control input-sm dateyear" type="text" name=tourist[birth] /></div>
            <div class="form-group form-group-sm"><input id="new-passport" class="form-control input-sm" type="text" name=tourist[passport] /></div>
            <div class="form-group form-group-sm"><input id="new-passdate" class="form-control input-sm dateyear" type="text" name=tourist[passdate] /></div>
            <div class="form-group"><input id="new-address" class="form-control input-sm" type="text" name=tourist[address] /></div>
            <input type="hidden" id="new-id" name="tourist[id]" value="" />
            <input type="hidden" id="order-Id" name="orderId" value="{$entity->getPk()}" />
            <input type="hidden" id="order-new" name="new" value="1" />
            <div class="form-group nosize action"><a data-record="#knownTourists" href="/orders/savetourist" class="glyphicon glyphicon-plus-sign green save-new"></a></div>
            <div class="searchform"><input id="sinput" class="form-control input-sm" type="text" placeholder="Введите ФИО, е-мейл или телефон" name="search"></div>
        </form>
    </div>

    <div id="wishes">
        <div class="form-group">
            <label for="transportWish">
                Пожелания к проезду
            </label>
            <input id="transportWish" value="{$entity->transportWish->get()}" class="col-lg-12 form-control" type="text" name="order[transportWish]"/>
        </div>

        <div class="form-group">
            <label for="hotelWish">
                Пожелания к проживанию
            </label>
            <input id="hotelWish" value="{$entity->hotelWish->get()}" class="col-lg-12 form-control" type="text" name="order[hotelWish]"/>
        </div>

        <div class="form-group">
            <label for="generalWish">
                Общие пожелания
            </label>
            <input id="generalWish" value="{$entity->generalWish->get()}" class="col-lg-12 form-control" type="text" name="order[generalWish]"/>

        </div>
    </div>




    {$comment}

    <script src="/js/frontend/order.js"></script>
    <h3>История заказа</h3>
    {$log}
</div>