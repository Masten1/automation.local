<html>
<head>
    <meta http-equiv="Content-Type" content=" application/vnd.ms-word; charset=UTF-8">
    <meta name=ProgId content=Word.Document>
    <meta name=Generator content="Microsoft Word 9">
    <meta name=Originator content="Microsoft Word 9">
    <style>
        {literal}
            body {font-family: Calibri, Arial, sans-serif}
            table {border-collapse: collapse; width: 100%}
            td, th {border: 1px solid black; padding: 5px}
            .left {font-weight: bold; float: left; margin-bottom: 5px}
            .right {font-weight: bold; float: right; margin-bottom: 5px}
            .main {clear: both}
        {/literal}
    </style>
</head>
<body>
    <h3>Рассчётный лист для тура {$tour->date->asString()}</h3>
<div class="section1">

    {foreach from=$tour->hotels->get() item=tHotel}
        {assign var=count value=0}
        <div class="left">{$tHotel->hotel->name->get()} ({$tHotel->getTouristCount()})</div>
        <div class="right">Хозяин: {$tHotel->hotel->agent->name} {$tHotel->hotel->agent->contacts}</div>
        <table class="main">
            <thead>
                <tr>
                    <th>№</th>
                    <th>ФИО туриста</th>
                    <th>Номер</th>
                    <th>Стоимость</th>
                    <th>Питание</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$tHotel->getTourists() item=tourist}
                {assign var=count value=$count+1}
                <tr>
                    <td>{$count}</td>
                    <td>{if $tourist->isGroupLeader($tour)}<b>{$tourist->name->get()}</b>{else}{$tourist->name->get()}{/if}</td>
                    <td>{$tourist->getHotelRoom($tour)}</td>
                    <td>{$tourist->getHotelRoomPrice($tour)} грн.</td>
                    <td>{$tHotel->hotel->foodPrice->get()} грн.</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <h5>Расчёт</h5>
        {assign var=total value=0}
        {assign var=nights value=$tour->offer->duration->get()-1}
        <table>
            <thead>
                <tr>
                    <th>Цена</th>
                    <th>Людей</th>
                    <th>На</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$tHotel->getOrderPrices() item=price}
                    <tr>
                        <td>{$price.price}</td>
                        <td>{$price.count}</td>
                        <td>{$nights}</td>
                        <td>{$price.price*$price.count*$nights}</td>
                    </tr>
                    {assign var=total value=$total+$price.price*$price.count*$nights}
                {/foreach}
                <tr>
                    {assign var=foodPrice value=$tHotel->hotel->foodPrice->get()}
                    {assign var=duration value=$tour->offer->duration->get()}
                    {assign var=food value=$foodPrice*$count*$duration}
                    <td>{$foodPrice}</td>
                    <td>{$tHotel->getTouristCount()}</td>
                    <td>{$duration}</td>
                    <td>{$food}</td>
                    {assign var=total value=$total+$food}
                </tr>
            </tbody>
        </table>
        <div class="right" style="page-break-after: always">Всего: {$total} грн.</div>
    {/foreach}
</div>
</body>
</html>