
    <form class="form-inline form-tourist" id="tourist-{$this->entity->getPk()}" action="/orders/savetourist">
        <input type="hidden" name="tourist[id]" value="{$this->entity->getPk()}">
        <input type="hidden" id="order-Id" name="orderId" value="{$entity->getPk()}" />
        <div class="form-group form-group-lg"><input class="form-control input-sm" type="text" placeholder="ФИО" name=tourist[name] value="{$this->entity->name->get()}"/></div>
        <div class="form-group"><input class="form-control input-sm phone" type="text" name=tourist[phone] value="{$this->entity->getPhone()}"/></div>
        <div class="form-group"><input class="form-control input-sm" type="text" name=tourist[email] value="{$this->entity->email->get()}"/></div>
        <div class="form-group form-group-sm"><input class="form-control input-sm dateyear" type="text" name=tourist[birth] value="{$this->entity->birth->get()}"/></div>
        <div class="form-group form-group-sm"><input class="form-control input-sm" type="text" name=tourist[passport] value="{$this->entity->passport->get()}"/></div>
        <div class="form-group form-group-sm"><input class="form-control input-sm dateyear" type="text" name=tourist[passdate] value="{$this->entity->passdate->get()}"/></div>
        <div class="form-group"><input class="form-control input-sm" type="text" name=tourist[address] value="{$this->entity->address->get()}"/></div>
        {assign var=main value=$entity->getMainTourist()}
        <div class="form-group nosize"><input type="checkbox" class="mainId" name="order[mainId]" value="{$this->entity->getPk()}" {if $main->getPk() == $this->entity->getPk()}checked{/if} ></div>
        <div class="form-group nosize action documents-edit"><span class="glyphicon glyphicon-file"></span></div>
        <div class="form-group nosize action"><a data-record="#knownTourists" href="/orders/savetourist" class="glyphicon glyphicon-plus-sign green save-new"></a></div>
        <div class="form-group nosize action"><a data-record="#tourist-{$this->entity->getPk()}" href="/orders/removetourist/{$entity->getPk()}/{$this->entity->getPk()}" class="glyphicon glyphicon-remove-circle red msglink"></a></div>
    </form>
