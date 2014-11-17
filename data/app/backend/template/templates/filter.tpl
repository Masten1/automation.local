<div style='width: 100%'>
    <form id='filter' method='post' action='/backend/{$module}/{$action}/' onsubmit='return true;'>
        <div class='filter_form' style='margin-bottom: 10px;'>

            <div class="operation">
                <a href="javascript:void(0);" onclick="$('clear').value = 1; window.doSendForm();" class="delete">очистить</a>
                <a href="javascript:void(0);" onclick="$('clear').value = '';  window.doSendForm();" class="accept">применить</a>
            </div>
            <div style="clear: both;"></div>
            {foreach from=$fields item=field}
                {printData field=$field}
            {/foreach}
            <input type="hidden" id="clear" name="{$arrayName}[_clear]" value="">
        </div>
        <input type="hidden" id="page" name="page" value="{$page}" />
        <input type="hidden" id="direct" name="direct" value="" />
        <input type="hidden" id="field" name="field" value="" />
    </form>
</div>