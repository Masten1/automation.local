<div style="clear: both;">
    <div style="float: right">
        <A
            href="{$fvConfig->get('dir_web_root')}pages/?id={$onePage->getPk()}"
            onclick="go('{$fvConfig->get('dir_web_root')}pages/?id={$onePage->getPk()}'); return false;"
            >
            <img src="{$fvConfig->get('dir_web_root')}img/edit_icon.png" title="редактировать" width="16" height="16">
        </a>
        {if $onePage->name ne 'default'}<a
            href="javascript: void(0);"
            onclick="if (confirm('Вы действительно желаете удалить страницу. Все дочерние страницы перенесутся в корень.')) go('{$fvConfig->get('dir_web_root')}pages/delete/?id={$onePage->getPk()}'); return false;"
            >
            <img src="{$fvConfig->get('dir_web_root')}img/delete_icon.png" title="удалить" width="16" height="16">
        </a>
        {/if}
    </div>
    <div>
    &nbsp;&nbsp;&nbsp;{if $onePage->parentId->get() != 0}&nbsp;&nbsp;&rarr;{/if}{$onePage->name}
    </div>
</div>