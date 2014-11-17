<div class="form-group" id="div-id-{$fieldName}">
    <label for="id-{$fieldName}" class="col-lg-2 text-right">{$field->getName()}</label>
    <div class="col-lg-4 input-group">
        <input class="form-control datetime input-sm" type="text" id="id-{$fieldName}" name={$dataName}[{$fieldName}] value="{$field->asAdorned()}"/>
        <span class="input-group-addon glyphicon glyphicon-calendar"></span>
    </div>
</div>