<div class="form-group" id="div-id-{$fieldName}">
    <label for="id-{$fieldName}" class="col-lg-2 text-right">{$field->getName()}</label>
    <div class="col-lg-4">
        <input class="form-control input-sm" type="text" id="{$prefix}id-{$fieldName}" name={$dataName}[{$fieldName}] value="{$field->get()}"/>
    </div>
</div>