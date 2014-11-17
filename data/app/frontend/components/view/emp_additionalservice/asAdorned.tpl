<tr id="addService-{$this->entity->getPk()}">
    <td>{$this->entity->price->get()} грн</td>
    <td>{$this->entity->comment->get()}</td>
    <td><a data-container="#addService-{$this->entity->getPk()}" data-id="{$this->entity->getPk()}" href="/orders/deleteaservice" class="glyphicon glyphicon-remove-circle red delete-service"></a></td>
</tr>