<div style="margin-top: 30px;">
<h1>Список меню</h1>
    <div class="table_body">
        <table class="text">
            {foreach from=$currentModuleTree item=item }
                {if count($item.child_nodes) > 0}
                    <tr >
                        <td>
                            <h3>{$item.name}</h3>
                        </td>
                        <td style="width: 80%;vertical-align: middle;">
                                {foreach from=$item.child_nodes item=child key=child_key }
                                    <div style="float: left;margin-right: 10px; text-decoration: underline;">
                                        <a href="{$child.href}" onclick="go('{$child.href}'); return false;">
                                        {if strlen(trim($child.image_name)) > 0 }
                                            <img src="/backend/img/menu_icons/{$child.image_name}" alt="{$child.name}" align="left" style="margin-right: 5px;">
                                        {/if}
                                            {$child.name}
                                        </a>
                                    </div>
                                {/foreach}
                        </td>
                    </tr>
                {/if}                         
            {/foreach}
        </table>
    </div>
</div>