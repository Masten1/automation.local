<h1>Группы менеджеров</h1>

<div style="width: 100%">
    <div class="table_body">
        <table class="text">
            <tr>
                <th>Имя</th>
                <th>По умолчанию</th>
                <th>Описание</th>
                <th>Активность пользователей</th>
                <th>&nbsp;</th>
            </tr>
            {foreach item=UserGroup from=$UserGroups}
                <tr>
                    <td class="mixed">{$UserGroup->name->get()}</td>
                    <td class="mixed" style="font-style: italic;">{$UserGroup->isDefault->asAdorned()}</td>
                    <td class="mixed">{$UserGroup->info->get()}</td>
                    <td class="mixed">{$UserGroup->isActive->asAdorned()}</td>
                    <td>
                        <A
                            href="{$fvConfig->get('dir_web_root')}usergroups/edit/?id={$UserGroup->getPk()}"
                            onclick="go('{$fvConfig->get('dir_web_root')}usergroups/edit/?id={$UserGroup->getPk()}'); return false;"
                            >
                            <img src="{$fvConfig->get('dir_web_root')}img/edit_icon.png" title="редактировать" width="16" height="16">
                        </a>
                        {if !$UserGroup->isDefault->get()}
                            <a
                                href="javascript: void(0);"
                                onclick="if (confirm('Вы действительно желаете удалить группу?')) go('{$fvConfig->get('dir_web_root')}usergroups/delete/?id={$UserGroup->getPk()}'); return false;"
                                >
                                <img src="{$fvConfig->get('dir_web_root')}img/delete_icon.png" title="удалить" width="16" height="16">
                            </a>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
    {if $UserGroups->hasPaginate()}
        <div id="manager_param_paging" class="paging">
            {$UserGroups->showPager()}
            {literal}
                <script>
                    new Pager("manager_param_paging");
                </script>
            {/literal}
        </div>
    {/if}
    <div class="operation">
        <a href="{$fvConfig->get('dir_web_root')}usergroups/edit/" onclick="go('{$fvConfig->get('dir_web_root')}usergroups/edit/'); return false;" class="add">добавить</a>
        <div style="clear: both;"></div>

    </div>
</div>