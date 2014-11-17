<table>
    <tr>
        <td id="leftpanel" valign="top">
           {include file=pages.leftbar.tpl}
        </td>
        <td class="spacer">&nbsp;</td>
        <td id="datapanel">
            <FORM method="post" action="/backend/pages/save/">
                <div class="form">
                    <H1>{if $Page->isNew()}Добавление страницы{else}Редактирование страницы '{$Page->name}'{/if}</H1>

                    
                    <div id="tabs">
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
                            {if $Page->name eq 'default'}
                            <p>Эта страница используется как базовая для задания основных параметров, таких как:</p>
                            <ul class="num">
                                <li>Основные мета-теги (если те не указаны в новых страницах.)</li>
                                <li>Основные модули, которые будут присутствовать на новой странице</li>
                            </ul>
                            {else}
                            {$Page|parse:edit}
                            {/if}
                            
                            <fieldset class="ui-widget ui-state-default">
                                <legend class="ui-widget ui-state-default">Javascript/CSS</legend>
                                <table class="form" id="jscss">
                                    <tr>
                                        <td style="width: 50%;">
                                            <fieldset class="ui-widget ui-state-default">
                                                <legend class="ui-widget ui-state-default">Javascript <a href='javascript:void(0);' onclick='javascript:window.addJS();' title='добавить javascript' class="ui-widget ui-icon ui-icon-plus" style="display: inline-block;"><img src='/backend/img/add.png' /></a></legend>
                                                <div id='jsbody'>{foreach from=$Page->getJS() item=ex}<div><input style='width:80%;' type='text' name='data[main][js][]' value='{$ex|escape}' class="ui-widget ui-corner-all"><a class="ui-widget ui-icon ui-icon-eject" style="display: inline-block" href='javascript:void(0);' onclick='javascript:jQuery(this).parent().html("");jQuery(this).parent().hide();' title='удалить'><img src='/backend/img/delete.png' /></a></div>{/foreach}</div>
                                            </fieldset>
                                        </td>
                                        <td style="width: 50%;">
                                            <fieldset class="ui-widget ui-state-default">
                                                <legend class="ui-widget ui-state-default">CSS <a href='javascript:void(0);' onclick='javascript:window.addCSS();' title='добавить css'  class="ui-widget ui-icon ui-icon-plus" style="display: inline-block;"><img src='/backend/img/add.png' /></a></legend>
                                                <div id='cssbody'>{foreach from=$Page->getCSS() item=ex}<div><input style='width:80%;' type='text' name='data[main][css][]' value='{$ex|escape}'  class="ui-widget ui-corner-all"><a class="ui-widget ui-icon ui-icon-eject" style="display: inline-block"  href='javascript:void(0);' onclick='javascript:jQuery(this).parent().html("");jQuery(this).parent().hide();' title='удалить'><img src='/backend/img/delete.png' /></a></div>{/foreach}</div>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
                        {if $lLangs }
                        {foreach from=$lLangs item=lang name=lang_inner}
                        <div id="tabs-{$smarty.foreach.lang_inner.iteration+1}" class="form">
                            {$Page|parse:edit:$lang}
                        </div>
                        {/foreach}
                        {/if}
                    </div>




                    <script>
                        {literal}
                        jQuery(function($){
                            $( "#tabs" ).hide();
                            $( "#tabs" ).tabs().fadeIn();
                        });
                        {/literal}
                    </script>

                    <div class="operation">
                        <a href="javascript: void(0);" class="content_edit" id="content_edit"
                            onclick=""
                            >управление содержимым страницы</a>
                        <br clear="all">
                    </div>

                    <div class="buttonpanel">
                        <input type="submit" name="save" value="Сохранить" class="ui-button" >
                    </div>
                    <input type="hidden" name="id" id="id" value="{$Page->getPk()}" />
                    <input type="hidden" name="data[main][content]" id="page_content" value="{if !$Page->isNew()}{$Page->getPageContent()|escape}{/if}" />
                </div>
            </FORM>
        </td>
    </tr>
</table>
<div id='jscont' style='display:none;'><div><input style='width:80%;' type='text' name='data[main][js][]'  class="ui-widget ui-corner-all"><a class="ui-widget ui-icon ui-icon-eject" style="display: inline-block" href='javascript:void(0);' onclick='javascript:jQuery(this).parent().html("");jQuery(this).parent().hide();' title='удалить'><img src='/backend/img/delete.png' /></a></div></div>
<div id='csscont' style='display:none;'><div><input style='width:80%;' type='text' name='data[main][css][]'  class="ui-widget ui-corner-all"><a class="ui-widget ui-icon ui-icon-eject" style="display: inline-block" href href='javascript:void(0);' onclick='javascript:jQuery(this).parent().html("");jQuery(this).parent().hide();' title='удалить'><img src='/backend/img/delete.png' /></a></div></div>
<div style="display: none" id="headers_content">
    <div class="popup_content">
        <a href="javascript: void(0)">SERVER_NAME</a> - заголовок статической страницы<br />
        <a href="javascript: void(0)">PUBLICATION_NAME</a> - заголовок публикации страницы<br />
        <a href="javascript: void(0)">PUBLICATION_TEXT</a> - описание публикации страницы<br />
        <a href="javascript: void(0)">PUBLICATIONTYPE_NAME</a> - заголовок тип публикации страницы<br />
        <a href="javascript: void(0)">PUBLICATIONTYPE_TEXT</a> - описание типа публикации страницы<br />
        <a href="javascript: void(0)">RUBRIC_NAME</a> - заголовок статической страницы<br />
        <a href="javascript: void(0)">RUBRIC_TEXT</a> - заголовок статической страницы<br />
        <a href="javascript: void(0)">COUNTRY_NAME</a> - заголовок статической страницы<br />
        <a href="javascript: void(0)">COUNTRY_TEXT</a> - заголовок статической страницы<br />
        <a href="javascript: void(0)">SUBRUBRIC_NAME</a> - заголовок статической страницы<br />
        <a href="javascript: void(0)">SUBRUBRIC_TEXT</a> - заголовок статической страницы<br />
    </div>
</div>

{literal}
<script>
    Object.extend(window, {
        insertArea: '',

        addJS: function()
        {
            var elem = document.createElement("div");
            elem.innerHTML = $("jscont").innerHTML;
            jQuery("#jsbody").append(elem);
        },

        addCSS: function()
        {
            var elem = document.createElement("div");
            elem.innerHTML = $("csscont").innerHTML;
            jQuery("#cssbody").append(elem);
        }
    });

    function moveLeftPanel (e) {
        if ($('leftpanel').getDimensions().width > 100) {
            $('leftpanel').morph('width: 20px;');
            $('leftPanelHeader').update("");
            $('leftpaneldata').hide();
            $('collapse').src = '{/literal}{$fvConfig->get('dir_web_root')}img/expand.gif{literal}';
        } else {
            $('leftpanel').morph('width: 300px;');
            $('collapse').src = '{/literal}{$fvConfig->get('dir_web_root')}img/collapse.gif{literal}';
            setTimeout("$('leftPanelHeader').update('Список страниц')", 1000);
            setTimeout("$('leftpaneldata').show()", 1000);
        }
    }

    var helpwindow = new PopUpWindow({
        width: 400,
        height: 300,
        center: true,
        title: "заголовки",
        name: 'headers',
        buttons: ['cancel'],
        contentData: $('headers_content').innerHTML,
        zIndex: 120
    });

    $$("table#metatags tr td a").each(function (link){
        link.observe("click", function (evt) {
            helpwindow.show();
        });
    })

    $$('div.popup_content a').each(function (link) {
        link.observe('click', function (evt) {
            elem = Event.element(evt);
            helpwindow.close();
            if ($(window.insertArea)) {
                $(window.insertArea).value = $F(window.insertArea) + '%' + elem.innerHTML + '%';
            }
        })
    });

    var wnd = new PopUpWindow({
        width: 800,
        height: 600,
        center: true,
        url: '/backend/',
        title: "управление содержимым",
        name: 'content_edit',
        zIndex: 100,
        onShow: function (params) {
            new Ajax.Updater('content_edit_content', '{/literal}{$fvConfig->get('dir_web_root')}pages/contentedit/{literal}', {
                parameters:
                {
                    _xmlContent: $F("page_content") || '{/literal}{$Page->getPageContent()|replace:"\n":""}{literal}'.replace("&lt;?xml version=&quot;1.0&quot;?&gt;", "&lt;?xml version=&quot;1.0&quot;?&gt;\n")
                },
                evalScripts: true
            });
        },
        onOk: function (params) {
            $('page_content').value = $F('_xmlContent');
        }
    });

    $('leftpaneldiv').setStyle({
        height: (document.viewport.getHeight() - $('header').getDimensions().height - 25) + "px"
    });

    $('hidePanel').observe('click', moveLeftPanel);
    $('content_edit').observe('click', wnd.show.bind(wnd));
</script>
{/literal}