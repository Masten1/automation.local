<div class="form-group">
    <label for="id-{$fieldName}" class="col-lg-2 text-right">{$field->getName()}</label>
    <div class="col-lg-4">
        <textarea class="form-control" id="id-{$fieldName}" name={$dataName}[{$fieldName}] >{$field->get()}</textarea>
    </div>
</div>