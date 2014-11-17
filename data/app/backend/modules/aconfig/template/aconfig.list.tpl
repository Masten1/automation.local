<h1>Глобальные константы</h1>

<div style="width: 100%">
    <div class="table_body">
        <table class="text">
            <tr class="ui-corner-all">
                <th width="30%">Константа</th>
                <th>Значение</th>
                <th width="170px;">Изменена</th>

                <th width="20px;">&nbsp;</th>
            </tr>
            {foreach item=iDictionary from=$cDictionary}
            <tr style="background: {cycle values="#fff,#eee"};">

                <td style="text-align: left; font-family: Consolas, monospace; font-size: 14px;">{$iDictionary->keyword}</td>
                <td><input type="text" value="{$iDictionary->value}" name="data[value]"></td>      
                <td class="mixed">{$iDictionary->mtime|date_human:ru:true}</td>         
                <td>
                    <form action="{$module}/save/" method="get" class='dict'>
                        <input type="hidden" name="id" value="{$iDictionary->getPk()}">
                        <input class="d-submit ui-icon ui-icon-circle-check" type="submit" style="display: inline-block; border: none;">
                    </form>
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
                var t = o.parents("tr").eq(0);
                
                $.ajax({
                    url: f.attr("action"),
                    data: f.serialize(),
                    type: "post",
                    success: function( data, status, xhr )
                    {
                        var m = $.parseJSON( xhr.getResponseHeader( "actionmessage" ) );
                        window.showActionMessage( m.message, m.type );
                        $(t).effect("highlight");
                    }
                });
                return false;
            });
        });
    {/literal}
</script>
