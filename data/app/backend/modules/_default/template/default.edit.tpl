<h1>
    {if $subject->isNew()}
        {$fvConfig->getModuleName($path)} → Создание записи
    {else}
        {$fvConfig->getModuleName($path)} → Редактирование записи
    {/if}
</h1>
<div class="operation"><a href="{$fvConfig->get('dir_web_root')}{$previous}/" onclick="go('{$fvConfig->get('dir_web_root')}{$previous}/'); return false;" class="left">вернутся к списку</a><div style="clear: both;"></div></div>
<form method="post" action="{$fvConfig->get('dir_web_root')}{$path}/save/">
    {if $subject->isLanguaged()}
        <div id="tabs" style="dislay: none">
            <ul>
                <li>
                    <a href="#tabs-1">Общая информация</a>
                </li>
                {if $lLangs }
                    {foreach from=$lLangs item=lang name=lang_title}
                        <li><a href="#tabs-{$smarty.foreach.lang_title.iteration+1}">{$lang->name}</a></li>
                    {/foreach}
                {/if}
            </ul>

            <div id="tabs-1" class="form">
                {$subject|parse:edit}
            </div>
            {if $lLangs }
                {foreach from=$lLangs item=lang name=lang_inner}
                    <div id="tabs-{$smarty.foreach.lang_inner.iteration+1}" class="form">
                        {$subject|parse:edit:$lang}
                    </div>
                {/foreach}
            {/if}
        </div>
    {else}
        <div class="ui-tabs ui-widget ui-widget-content ui-corner-all"><div class="form">
                {$subject|parse:edit}
            </div></div>
        {/if}
    <br/>
    <div class="buttonpanel">
        <input type="hidden" name="previous" value="{$previous}" />
        <input type="hidden" name="redirect" id="redirect" value="">
        <input type="hidden" name="id" value="{$subject->getPk()}">
        <input type="submit" name="save" value="Сохранить" class="ui-button"  onclick="$('redirect').value = '';">
        <input type="submit" name="save_and_return" value="Сохранить и выйти" class="ui-button" onclick="$('redirect').value = '1';">
    </div>

</form>

<script>
    {literal}
    jQuery(function($) {
        /*$("#phone").mask("+380 (99) 999-99-99");*/
        tinyMCE.init({
            // General options
            mode : "textareas",
            editor_selector: "rich",
            theme : "advanced",
            width: 900,
            height: 400,
            plugins : "imagemanager,filemanager,style,layer,table,save,advhr,advimage,advlink,inlinepopups,preview,media,searchreplace,print,contextmenu,directionality,fullscreen,wordcount,autosave",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,|,fullscreen",
            theme_advanced_buttons2 : "bullist,numlist,|,link,unlink,anchor,cleanup,code,|,forecolor,backcolor,|, tablecontrols,|,advhr,removeformat,|,insertimage, insertfile, image,charmap,media",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,


            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "lists/template_list.js",
            external_link_list_url : "lists/link_list.js",
            external_image_list_url : "lists/image_list.js",
            media_external_list_url : "lists/media_list.js"
        });

        $(".date" ).datepicker({ dateFormat: 'yy-mm-dd' });
        $( "#tabs" ).tabs();
    });
    {/literal}
</script>