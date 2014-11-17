<html>
<head>
    <meta http-equiv="Content-Type" content=" application/vnd.ms-word; charset=UTF-8">
    <meta name=ProgId content=Word.Document>
    <meta name=Generator content="Microsoft Word 9">
    <meta name=Originator content="Microsoft Word 9">
    <style>
        {literal}
            body {font-family: Calibri, Arial, sans-serif}
            .section1 {text-align: center}
            table {border-collapse: collapse}
            td, th {border: 1px solid black; padding: 5px}
        {/literal}
    </style>
</head>
<body>
    {assign var=count value=1}
    <h3>Список застрахованих осіб до договору страхування №</h3>
    <p>Напрямок подорожі – гк Буковель, с. Поляниця, Івано-Франківської обл.</p>
<div class="section1">
    <table class="main">
        <thead>
            <tr>
                <th>#</th>
                <th>Прізвище, ім'я, по-батькові</th>
                <th>Тур</th>
                <th>Услуга</th>
                <th>Адреса</th>
                <th>Дата народження</th>
                <th>Телефон</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$insurance->getTourists() item=tourist}
            <tr>
                <td>{$count}</td>
                <td>{$tourist->name->get()}</td>
                <td>{$insurance->order->tour->date->asAdorned()}</td>
                <td>{$insurance->offservice->service}</td>
                <td>{$tourist->address->get()}</td>
                <td>{$tourist->birth->asAdorned()}</td>
                <td>{$tourist->getPhone()}</td>
            </tr>
            {assign var=count value=$count+1}
        {/foreach}
        </tbody>
    </table>
</div>
    <p class="left">Страхувальник Шаповалов В. В. _______</p>
</body>
</html>