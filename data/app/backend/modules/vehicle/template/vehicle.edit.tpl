<h1>
    {if $subject->isNew()}
        {$fvConfig->getModuleName($path)} → Создание записи
    {else}
        {$fvConfig->getModuleName($path)} → Редактирование записи
    {/if}
</h1>
<div id="back" class="operation"><a href="{$fvConfig->get('dir_web_root')}{$previous}/" onclick="go('{$fvConfig->get('dir_web_root')}
                        {$previous}/'); return false;" class="left">вернутся к списку</a><div style="clear: both;"></div></div>
<form method="post" action="{$fvConfig->get('dir_web_root')}{$path}/save/">

    <div class="section-left">
        <div class="control">
            <label for="number">Номер автобуса</label>
            <input type="text" id="number" name="data[number]" value="{$subject->number->get()}" />
        </div>

        <div class="control">
            <label for="model">Модель автобуса</label>
            <input type="text" id="model" name="data[model]" value="{$subject->model->get()}" />
        </div>

        <div class="control">
            <label for="totalPlaces">Общее к-во мест</label>
            <input type="text" id="totalPlaces" name="data[totalPlaces]" value="{$subject->totalPlaces->get()}" />
        </div>

        <div class="control">
            <label for="width">Ширина автобуса</label>
            <input type="text" id="width" name="data[width]" value="{$subject->width->get()}" />
        </div>

        <div class="control">
            <input type='hidden' value='0' name='data[secondTier]'>
            <label for="hasSecond">
                <input type="checkbox" id="sTierTrigger" name="data[secondTier]" value="1" {if $subject->secondTier->get()}checked{/if} />
                Есть второй ярус
            </label>
        </div>

        <div class="control">
            <label for="firstTier">
                Длина первого яруса
            </label>
            <input type="text" name="tier[1][{$subject->getFirstTierId()}][rows]" id="firstTier" value="{$subject->getFirstTierRows()}" />
        </div>

        <div id="sTierContainer" class="control{if !$subject->secondTier->get()} hidden{/if}">
            <label for="secondTier">
                Длина второго яруса
            </label>
            <input type="text" name="tier[2][{$subject->getSecondTierId()}][rows]" id="secondTier" value="{$subject->getSecondTierRows()}"/>
        </div>

        <div class="control">
            <input type='hidden' value='0' name='data[isActive]'>
            <label class="checkbox" for="isActive">
                <input type="checkbox" value="1" name="data[isActive]" {if $subject->isActive->get()}checked{/if}/>
                Отображать в справочнике
            </label>
        </div>

    </div>

    <div class="section-right">
        {if !$subject->isNew()}
            {$subject->widget()}
        {/if}
    </div>


    <div class="buttonpanel">
        <input type="hidden" name="redirect" id="redirect" value="">
        <input type="hidden" name="id" value="{$subject->getPk()}">
        <input type="submit" name="save" value="Сохранить" onclick="$('redirect').value = '';">
        <input type="submit" name="save_and_return" value="Сохранить и выйти" onclick="$('redirect').value = '1';">
    </div>

</form>

<script>
    {literal}
    jQuery(function($) {
        $("#sTierTrigger").click(function(){
            $("#sTierContainer").toggle();
        });

        $(".seat-td").on("click", function(event){
            event.stopPropagation();
            $(".seat-input-pad").remove();
            var container = $("<div />").addClass("seat-input-pad");
            $("<p>Место:</p>").appendTo(container);
            $("<input type='text'>").attr("id", "seatInput").val($(this).children(".seat").data("val")).appendTo(container);
            $("<p>Коммент:</p>").appendTo(container);
            $("<input type='text'>").attr("id", "comTextInput").val($(this).children(".seat").data("comment")).appendTo(container);
            $("<button></button>").attr("id", "seatSubmit").html("OK").appendTo(container);
            container.appendTo( $(this).children(".seat") );
            $("#seatInput").focus();
        }).on("click", "#seatSubmit", function(e){
            e.preventDefault();
            e.stopPropagation();
            var seat = $(this).parents(".seat");
            var number = $("#seatInput").val();
            var comText = $(this).parents(".comText");
            var comment = $('#comTextInput'). val();
            $.ajax({
                url: "/backend/vehicles/seat",
                type: "POST",
                data: {
                    "data[tierId]": seat.data("tier"),
                    "data[row]": seat.data("row"),
                    "data[column]": seat.data("column"),
                    "number": number,
                    "comment": comment
                },
                success: function() {
                    seat.html(number);
                }
            });
        }).on("click", "#seatInput, #comTextInput", function(e){
            e.stopPropagation();
        });

        $("body").on("click", function(){
            $(".seat-input-pad").remove();
        });

    });

    {/literal}
</script>