<h1>Словарь</h1>

<div class="field-box"><div>
    <form id="search">
        Фильтр: <input type="text" value="{$search}" style="width: 170px"/> <button type="submit">Фильтровать</button>
    </form>
</div></div>

<script>{literal}
jQuery(function($){
    $("#search").submit(function(){
        window.location.hash = window.location.hash.toString().replace(/\?.*/,'') + '?search=' + $(this).children('input').val()
        return false;
    });
});
{/literal}</script>

<div style="width: 100%">
    <div class="table_body">
        <table class="text">
            <tr>
                <th>Ключ</th>
                
                {foreach from=$cLanguages item=iLanguage}
                <th>{$iLanguage->name}</th>
                {/foreach}

                <th width="50px;">&nbsp;</th>
            </tr>
            {foreach item=iDictionary from=$cDictionary}
            <tr>

                <td class="mixed">{$iDictionary->keyword}</td>
                {foreach from=$cLanguages item=iLanguage key=langName}
                    {$iDictionary->setLanguage($iLanguage)}
                    <td><input type='text' id='{$name}' value='{$iDictionary->translation->get()|htmlentities:true:'UTF-8'}' name='data[{$iLanguage->code}][translation]'></td>
                {/foreach}
                <td>
                    <form action="{$module}/save/" method="get" class='dict'>
                        <input type="hidden" name="id" value="{$iDictionary->getPk()}">
                        <input class="d-submit ui-icon ui-icon-circle-check" type="submit" style="display: inline-block; border: none;">
                    </form>
                    
                    <a href="javascript: void(0);" 
                        onclick="if (confirm('Вы действительно желаете удалить страницу?')) go('{$fvConfig->get('dir_web_root')}{$module}/delete/?id={$iDictionary->getPk()}'); return false;"
                        class="ui-icon ui-icon-circle-close" style="display: inline-block; border: none;">
                        <img src="{$fvConfig->get('dir_web_root')}img/delete_icon.png"  title="удалить" width="16" height="16">
                    </a>
                </td>

            </tr>
            {/foreach}
        </table>
    </div>
    {if $cDictionary->hasPaginate()}
    <div id="manager_param_paging" class="paging">
        {$cDictionary->showPager()}
        {literal}
        <script>
            new Pager("manager_param_paging");
        </script>
        {/literal}
    </div>
    {/if}
</div>

<script>
    {literal}
        jQuery(function($){
            $("input.d-submit").click(function(){
                var o = $(this).parents("form");
                var f = $("<form></form>").attr({ action:  o.attr("action") });
                
                o.parents("tr").find("input[type=text], input[type=hidden]").each(function(){
                    $(this).clone().appendTo( f );    
                });
                
                $.ajax({
                    url: f.attr("action"),
                    data: f.serialize(),
                    type: "post",
                    success: function( data, status, xhr )
                    {
                        var m = $.parseJSON( xhr.getResponseHeader( "actionmessage" ) );
                        window.showActionMessage( m.message, m.type );
                    }
                });
                return false;
            });
            
            $(".text input").change( function(){
                $(this).data('borderColor', $(this).css('borderColor')).css({borderColor: 'orange'});
                    
            } );
            $(".text input[type=submit]").click( function(){
                $(this).parents('tr').find('input').each(function(){
                    $(this).css({borderColor: $(this).css('borderColor')});
                });
            });
        });
    {/literal}
</script>
