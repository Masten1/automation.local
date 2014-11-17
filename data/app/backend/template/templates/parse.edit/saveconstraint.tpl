<label for="{$name}-constraint">{$field->getName()}</label>

<div id="existingOffices">
    {foreach from=$field->get() item=const}
        <div class="constraint-block-{$const->getPk()}">
            {foreach from=$const->getFields() key=cName item=cField}
                {if $cField->isEditable() && $cField->isCEditable()}
                    <div style="display: inline-block;">
                        <label style="font-size: 13px" for="const-{$const->getPk()}-{$cName}">{$cField->getName()}</label>
                        <input id="const-{$const->getPk()}-{$cName}" name="constraint[{$cName}]" type="text" value="{$cField}">
                    </div>
                {/if}
            {/foreach}
            <input type="hidden" name="constraint[id]" value="{$const->getPk()}" >
            <input type="hidden" name="constraint[{$field->getForeignEntityKey()}]" value="{$subject->getPk()}" />
            <a href="javascript:void(0)" class="d-submit ui-icon ui-icon-circle-check" style="display: inline-block; border: none;" data-name="{$field->getEntity()}" ></a>
            <a href="javascript:void(0)" data-name="{$field->getEntity()}" data-id="{$const->getPk()}" class="c-delete ui-icon ui-icon-circle-close" style="display: inline-block; border: none;">
            </a>
        </div>
    {/foreach}
</div>

{assign var=cEntity value=$field->generateForeignEntity()}

<div id="add-con-{$field->getEntity()}">
    <label>Добавить новую запись</label>
    <div id="container-{$field->getEntity()}">
    {foreach from=$cEntity->getFields() key=cName item=cField}
        {if $cField->isEditable() && $cField->isCEditable()}
            <div style="display: inline-block;">
                <label style="font-size: 13px" for="constraint-new-{$cName}">{$cField->getName()}</label>
                <input id="constraint-new-{$cName}" name="constraint[{$cName}]" type="text">
            </div>
        {/if}
    {/foreach}
    </div>
    <input type="hidden" name="constraint[{$field->getForeignEntityKey()}]" value="{$subject->getPk()}" />

    <a href="javascript:void(0)" class="addSingle" data-new="1" data-name="{$field->getEntity()}">
        Добавить значение
    </a>
</div>

<script>
    {literal}
    jQuery(function ($) {
        $(".addSingle, .d-submit").click(function () {
            var path = "{/literal}{$path}{literal}";
            var $this = $(this);
            $.post("/backend/"+path+"/addconstraint/?name="+$this.data('name'), $this.parent().find("input").serialize(), function(){
                if($this.data('new') == 1) {
                    $("#container-"+$this.data('name')).clone(true).attr('id', "").appendTo($("#existingOffices"));
                    $this.parent().find("input[type='text']").val("")
                }
                showActionMessage("Данные обновлены", 'success');
            });
        });

        $(".c-delete").click(function(){
            if (confirm('Вы действительно желаете удалить запись?')) {
                var path = "{/literal}{$path}{literal}";
                var $this = $(this);
                $.post("/backend/"+path+"/rmvconstraint/?name="+$this.data('name') , {cid : $this.data("id")}, function(){
                    $this.parent().remove();
                    showActionMessage("Запись успешно удалена", 'success');
                });
            }
        });
    });
    {/literal}
</script>