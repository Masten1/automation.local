<div id="leftpaneldiv">
    <a href="javascript:void(0);" id="hidePanel"><img id="collapse" src="{$fvConfig->get('dir_web_root')}img/collapse.gif" width="15" height="15"></a>
    <div class="header" id="leftPanelHeader">Список страниц</div>
    <div id="leftpaneldata" style=" overflow-y: scroll; height: 90%; ">
        {foreach from=$pageTree item=domainPool}
            {assign var=iDomain value=$domainPool.domain}
            {assign var=cPage value=$domainPool.pages}
           
            <div class="domain-stack">
                <a class="trigger-stack" style="font-size: 120%; border-bottom: 1px black dashed;">{$iDomain}</a>
                <div>
                    {foreach from=$cPage item=family}
                        {assign var=onePage value=$family.parent}

                        {include file=pages.row.tpl onePage=$onePage}
                        {foreach from=$family.nodes item=page}
                               {include file=pages.row.tpl onePage=$page}
                        {/foreach}
                    {/foreach}
                </div>
            </div>
        {/foreach}
        <div class="operation">
            <a href="{$fvConfig->get('dir_web_root')}pages/" onclick="go('{$fvConfig->get('dir_web_root')}pages/'); return false;" class="add">добавить</a>
        </div>
</div>
        
    <script>
    {literal}
    jQuery(function($){
        $("div.domain-stack a.trigger-stack").click(function(){
            $("div.domain-stack > div").slideUp();          
            $(this).parents("div.domain-stack").find("div").slideDown();
        });
    });
    {/literal}
    </script>