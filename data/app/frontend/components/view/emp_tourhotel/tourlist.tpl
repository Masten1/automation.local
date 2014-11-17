<div data-hotel="{$this->entity->getPk()}" class="hotel-droppable" id="hotel-tourists-{$this->entity->getPk()}">
    <strong>{$this->entity->hotel->name->get()}</strong>
    <div class="tourlist-head">
        <div class="col-lg-3">ФИО</div>
        <div class="col-lg-3">Руководитель</div>
        <div class="col-lg-2">Телефон</div>
        <div class="col-lg-2">Где живет</div>
        <div class="col-lg-2">Чем едет</div>
    </div>
    {foreach from=$this->entity->getTourists($tour->getPk()) item=tourist}
        {$tourist->render("noservice")}
    {/foreach}
</div>