{if !$order->isNew()}
    <div class="btn-group btn-group-justified">
        <a class="btn btn-primary navlink" href="/orders/edit/{$order->getPk()}">Заявка</a>
        <a class="btn btn-primary navlink" href="/orders/services/{$order->getPk()}">Договор</a>
        <a class="btn btn-primary navlink" href="/orders/pay/{$order->getPk()}">Платежи</a>
        <a class="btn btn-primary navlink" href="/orders/transfer/{$order->getPk()}">Посадка/Поселение</a>
        <a class="btn btn-primary navlink active" href="/orders/ok/{$order->getPk()}">OK!</a>
    </div>
{/if}

{$comment}
<h3>История заказа</h3>
{$log}