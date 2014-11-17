<div id="vehicle-container-{$this->entity->vehicle->getPk()}" data-transport="{$this->entity->vehicle->getPk()}" class="transport-container">
    <strong class="text-ul">{$this->entity->vehicle->model}</strong>
    <div class="info">К-во спальных мест: <strong>{$this->entity->vehicle->sleepingCapacity->get()}</strong></div>
    <div class="info">К-во сидячих мест: <strong>{$this->entity->vehicle->sittingCapacity->get()}</strong></div>
    <div class="info">К-во дополнительных мест: <strong>{$this->entity->vehicle->extraCapacity->get()}</strong></div>
    <div class="info">Всего мест: <strong>{$this->entity->vehicle->getTotalPlaces()}</strong></div>
    <div class="updating">Занято мест: <strong class="toupdate">{$this->entity->tour->getBusy("Emp_OrderTransport", "vehicleId", $this->entity->vehicle->getPk())}</strong></div>
    <div class="route">
        <strong class="text-ul">Маршрут:</strong>
    </div>
</div>