<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name=ProgId content=Word.Document>
    <meta name=Generator content="Microsoft Word 9">
    <meta name=Originator content="Microsoft Word 9">
    <style>
        {literal}
            div.section1 {width: 1120px}
            table {border-collapse: collapse;}
            td, th {padding: 0}
            .main, .info, .finance, .route, .empty {width: 100%}
            .mainblock: {width: 75%}
            .comments  {width: 25%; height: 100%; vertical-align: top}
            th {text-align: center; height: 20px; background: rgb(251,212,180)}
            .border th, .border td {border: 1px solid black}
            .comments table {width: 100%; height: 100%;}
            .comments td { padding: 5px;}
            .comments table td {vertical-align: top}
            .info td, .finance td, .route td {padding: 0 2px; text-align: center}
            .empty td, .empty th {padding: 7px;}
            .empty {margin-top: 20px;}
            h5 {margin-bottom: 5px;}
        {/literal}
    </style>
</head>
<body>
<div class="section1">
    <h2>{$tr->tour}</h2>
    <strong>{$tr}</strong>, <strong>Водитель:</strong> {$tr->driver}, <strong>Агент:</strong> {$tr->vehicle->agent}
    {assign var=count value=0}
    <table class="main">
        {foreach from=$tr->getOrders() item=order}
        <tr>
            <td class="mainblock">
                <table class="info border">
                    <tr>
                        <th style="width: 20px;">#</th>
                        <th style="width: 100px;">Посадка</th>
                        <th style="width: 150px">Отель</th>
                        <th>Проживание</th>
                        <th>Главный</th>
                        <th>Туристы</th>
                        <th style="width: 100px">Телефоны</th>
                        <th style="width: 80px">Сумма</th>
                    </tr>
                    {foreach name=oTour from=$order->getTransportTourists($tr) item=tourist}
                        {assign var=count value=$count+1}
                        <tr>
                            <td>{$count}</td>
                            {assign var=oHotel value=$tourist->getOrderHotel($tr->tour)}
                            {assign var=oTrans value=$tourist->getOrderTransport($tr->tour)}
                            <td>
                                <b>{$oTrans->seat}</b><br />
                                {if $oTrans}{$oTrans->getComment($order)}{else}{$order->transportWish}{/if}
                            </td>
                            <td>
                                {$oHotel->tourhotel}<br />
                                <b>{$oHotel->room}</b>
                            </td>
                            <td>{if $oHotel}{$oHotel->comment}{else}{$order->hotelWish}{/if}</td>
                            <td>{$tourist->getGroupLeader($tr->tour)}</td>
                            <td>{$tourist->getName()}</td>
                            <td>{$tourist->getPhone()}</td>
                            <td>{$tourist->getCost($order)} грн</td>
                        </tr>
                    {/foreach}
                </table>
                <table class="finance border">
                    <tr>
                        <th>Сумма</th>
                        <th>Оплачено</th>
                        <th>Осталось</th>
                        <th>Комментарии</th>
                    </tr>
                    <tr>
                        <td>{$order->getPrice()} грн</td>
                        <td>{$order->getPayment()} грн</td>
                        <td>{$order->getRest()} грн</td>
                        <td></td>
                    </tr>
                </table>
            </td>
            <td class="comments border">
                <table>
                    <tr>
                        <th>Комментарии</th>
                    </tr>
                    <tr>
                        <td>
                            {foreach from=$order->comments->get(null, "ctime desc") item=comment}
                                <div>{$comment}</div>
                            {/foreach}
                            {foreach from=$order->getTransportTourists($tr) item=tourist}
                                {if $tourist->getInsurance($order)}
                                    <h5>{$tourist->name} - Страховка</h5>
                                    {$tourist->getInsurance($order)}
                                {/if}
                                {if $tourist->getSkipass($order)}
                                    <h5>{$tourist->name} - Скипасс</h5>
                                    {$tourist->getSkipass($order)}
                                {/if}
                            {/foreach}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {/foreach}
    </table>

    <h2>{$tr->tour}</h2>
    <strong>{$tr}</strong>, <strong>Водитель:</strong> {$tr->driver}, <strong>Агент:</strong> {$tr->vehicle->agent}
    <table class="route border">
        <tr>
            <th>Название</th>
            <th>Контактное лицо</th>
            <th>Адрес</th>
            <th>К-во человек</th>
        </tr>
        {foreach from=$tr->getRouteHotel() item=tHotel}
            <tr>
                <td>{$tHotel->hotel->name->get()}</td>
                <td>{$tHotel->hotel->agent->name->get()}, {$tHotel->hotel->contacts->get()}</td>
                <td>{$tHotel->hotel->address->get()}</td>
                <td>{$tHotel->getCountByTransport($tr->getPk())}</td>
            </tr>
        {/foreach}
    </table>

    <table class="empty border">
        <tr>
            <th style="width: 30px">№</th>
            <th>Доход/Расход</th>
            <th>Кто передал?</th>
            <th>Кому?</th>
            <th>Дата, Время</th>
        </tr>
        {section name=empty loop=15}
            <tr>
                <td>&nbsp</td>
                <td>&nbsp</td>
                <td>&nbsp</td>
                <td>&nbsp</td>
                <td>&nbsp</td>
            </tr>
        {/section}
    </table>
</div>
</body>
</html>