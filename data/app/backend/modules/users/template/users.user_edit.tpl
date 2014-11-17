<FORM method="post" action="/backend/users/save/">
    <div class="form">
        <H1>
            {if $User->isNew()}
                Добавление пользователя
            {else}
                Редактирование пользователя '{$User->login}'
            {/if}
        </H1>
        <div class="operation"><a href="{$fvConfig->get('dir_web_root')}users/" onclick="go('{$fvConfig->get('dir_web_root')}users/'); return false;" class="left">вернутся к списку</a><div style="clear: both;"></div></div>
        <div style="width: 50%; float: left;">

            <fieldset>
                <legend>Общая информация</legend>

                <label for="login">Логин</label>
                <input type="text" id="login" name="m[login]" value="{$User->login|escape}" {if !$User->isNew()}readonly="readonly"{/if}/>
                <br />

                <label for="group_id">Группа</label>
                {html_options name=m[groupId] id=groupId options=$GroupManager->htmlSelect('name', '', '', 'isDefault DESC') selected=$User->groupId}
                <br />

                <label for="email">Адрес электронной почты</label>
                <input type="text" id="email" name="m[email]" value="{$User->email|escape}" />
                <br />

                <label for="full_name">Имя</label>
                <input type="text" id="full_name" name="m[name]" value="{$User->name}" />
                <br />


                <label for="info">Phone</label>
                <input type="text" id="full_name" name="m[phone]" value="{$User->phone}" />
                <br />
            </fieldset>

            <fieldset>
                <legend>Пароль пользователя</legend>
                {if !$User->isNew()}
                    <p style="color: #922;">Оставте пароль пустым, если не хотите его изменять</p>
                {/if}
                <label for="password">Пароль</label> <input type="password" id="password" name="m[password]" /> <br />
                <label for="password1">Повторите пароль</label> <input type="password" id="password1" name="m[password1]" /> <br />
            </fieldset>

            <fieldset>
                <legend>Параметры пользователя</legend>

                <input type="checkbox" name="m[isRoot]" value="1" id="m_is_root" {if $User->isRoot->get()}checked="true"{/if}>
                <label for="m_is_root" class="checkbox">суперпользователь</label> <br />

                <input type="checkbox" name="m[isActive]" value="1" id="m_is_active" {if $User->isActive->get()}checked="true"{/if}>
                <label for="m_is_active" class="checkbox">активный пользователь</label> <br />

                <input type="checkbox" name="m[inherit]" value="1" id="m_inherit" {if $User->inherit->get()}checked="true"{/if}>
                <label for="m_inherit" class="checkbox">наследовать параметры из группы</label> <br />
            </fieldset>

            <div class="buttonpanel">
                <input type="submit" name="save" value="Сохранить" class="button"  onclick="$('redirect').value = '';">
                <input type="submit" name="save_and_return" value="Сохранить и выйти" class="button" onclick="$('redirect').value = '1';">
            </div>
            <input type="hidden" name="id" id="id" value="{$User->getPk()}" />
            <input type="hidden" id="redirect" name="redirect" value="" />
            <input type="hidden" id="site_parameters" name="site_parameters" value="" />
        </div>
        <div style="float: left; width: 40%; margin-left: 10px;">
            <div id="user_params" {if $User->isRoot->get()}style="display: none"{/if}>

                <fieldset>
                    <legend>Права доступа</legend>


                    <div  id="parameters_list">

                        {foreach item=acl_group from=$fvConfig->get('acls')}

                            <fieldset>
                                <legend>{$acl_group.name}</legend>

                                <ul class="acls">
                                    {foreach item=acl from=$acl_group key=acl_name}

                                        {if $acl_name ne 'name'}{if is_array($acl)}
                                                <li>
                                                    <input type="checkbox" name="" value="" id="m_permitions_{$acl_name}" {literal}onclick="$$('#content div.form ul.acls li input#m_permitions_{/literal}{$acl_name}{literal} + label + ul > li > input').each(function (el) {el.checked = $('m_permitions_{/literal}{$acl_name}{literal}').checked}); $('m_inherit').checked = false;"{/literal}>
                                                    <label for="m_permitions_{$acl_name}" class="checkbox">{$acl.name}</label>
                                                    <ul class="acls">
                                                        {foreach item=acl_chld from=$acl key=acl_chld_name}
                                                            {if $acl_chld_name ne 'name'}
                                                                <li>
                                                                    <input type="checkbox" name="m[permitions][]" value="{$acl_chld_name}" id="m_permitions_{$acl_chld_name}" {if in_array($acl_chld_name, $User->permitions->asArray())}checked="true"{/if} onchange="$('m_inherit').checked = false; $('m_permitions_{$acl_name}').checked = false;">
                                                                    <label for="m_permitions_{$acl_chld_name}" class="checkbox">{$acl_chld}</label>
                                                                </li>
                                                            {/if}
                                                        {/foreach}
                                                    </ul>
                                                </li>
                                            {else}
                                                <li><input type="checkbox" name="m[permitions][]" value="{$acl_name}" id="m_permitions_{$acl_name}" {if in_array($acl_name, $User->permitions->asArray())}checked="true"{/if} onchange="$('m_inherit').checked = false;">
                                                    <label for="m_permitions_{$acl_name}" class="checkbox">{$acl}</label></li>
                                            {/if}{/if}
                                        {/foreach}
                                </ul>
                            </fieldset>
                        {/foreach}

                    </div>
                </fieldset>
            </div>
        </div>
        <div style="clear: both;" />
    </div>
</FORM>

<script>

    {literal}
    Object.extend(window, {
        currentSiteId: 0,
        siteParameters: {},
        updateSitePermitions: function (obj) {
            if (!window.siteParameters[window.currentSiteId]) window.siteParameters[window.currentSiteId] = {};
            if (obj.checked)
                window.siteParameters[window.currentSiteId][obj.id] = $F(obj);
            else
                window.siteParameters[window.currentSiteId][obj.id] = null;

            $('site_parameters').value = Object.toJSON(window.siteParameters);
        }
    });


    function changeInherit(e) {
        if ($('m_inherit') && $('m_inherit').checked && $('m_global_rights').checked) {
            window.blockScreen();

            new Ajax.Request("{/literal}{$fvConfig->get('dir_web_root')}{literal}usergroups/getparams", {
                parameters: {'group_id': $F('group_id')},
                onComplete: function (response, json) {
                    if ($('contentblocker')) $('contentblocker').hide();
                    $$("div#parameters_list input").each(function (checkbox){
                        checkbox.checked = false;
                    });
                    $A(json).each(function (acl) {
                        if ($('m_permitions_'+acl)) {
                            $('m_permitions_'+acl).checked = true;
                        }
                    });
                }
            });
        }
    }


    $('m_is_root').observe('change', function (e) {
        if (Event.element(e) && Event.element(e).checked) {
            $('user_params').hide();
        } else {
            $('user_params').show();
        }
    });


    $("m_inherit").observe('change', changeInherit);
    $('group_id').observe('change', changeInherit);

    $('m_global_rights').observe('change', function (e) {
        elem = Event.element(e);

        if (elem.checked) {
            $('parameters_list').show();
            $('parameters_for_site').hide();
            $("m_inherit").disable = false;
        } else {
            $('parameters_list').hide();
            $('parameters_for_site').show();
            $('m_inherit').checked = false;
            $("m_inherit").disable = true;
        }
    });
    {/literal}
</script>

