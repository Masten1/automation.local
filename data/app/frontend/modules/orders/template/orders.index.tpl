<div class="row">
    <div class="form-group col-lg-4">
        <label for="tourFilter">
            Выберите тур:
        </label>
        <select class="form-control" id="tourFilter">
            <option value="">Все</option>
            {foreach from=$tourFilter item=filter}
                <option value="{$filter->getPk()}">{$filter}</option>
            {/foreach}
        </select>
    </div>
</div>


<div id="result" style="clear: both">
    {show_module module=orders view=result}
</div>