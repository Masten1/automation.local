{foreach item=acl_group from=$fvConfig->get('acls')}

<fieldset>
    <legend>{$acl_group.name}</legend>

<ul class="acls">
{foreach item=acl from=$acl_group key=acl_name}
    {if $acl_name ne 'name'}{if is_array($acl)} 
        <li> 
        <input type="checkbox" name="" value="" id="permitions_{$acl_name}" {literal}onclick="$$('#content div.form ul.acls li input#permitions_{/literal}{$acl_name}{literal} + label + ul > li > input').each(function (el) {el.checked = $('permitions_{/literal}{$acl_name}{literal}').checked; window.updateSitePermitions(el);});"{/literal}>
        <label for="permitions_{$acl_name}" class="checkbox">{$acl.name}</label>
        <ul class="acls">
        {foreach item=acl_chld from=$acl key=acl_chld_name}
            {if $acl_chld_name ne 'name'}
                <li><input type="checkbox" name="permitions[]" value="{$acl_chld_name}" id="permitions_{$acl_chld_name}" {if in_array($acl_chld_name, $Privileges->permitions)}checked="true"{/if} onchange="$('permitions_{$acl_name}').checked = false; window.updateSitePermitions(this);">
                <label for="permitions_{$acl_chld_name}" class="checkbox">{$acl_chld}</label></li>
            {/if}
        {/foreach}
        </ul>
        </li>
    {else}
        <li><input type="checkbox" name="m[permitions][]" value="{$acl_name}" id="permitions_{$acl_name}" {if in_array($acl_name, $Privileges->permitions)}checked="true"{/if} onchange="window.updateSitePermitions(this);">
        <label for="permitions_{$acl_name}" class="checkbox">{$acl}</label></li>
    {/if}{/if}
{/foreach}
</ul>
</fieldset>
{/foreach}

