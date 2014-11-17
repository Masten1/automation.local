<label>
    {$field->getName()}:
    <input type='text' id='{$name}' value='{$field->get()|htmlentities:true:'UTF-8'}' name='{$arrayName}[{$name}]'>
</label>
<a href='javascript:void(0);' onclick="javascript:window.doGenerateUrl('{$name}', '{$field->getAuto()}');">
    <p style='font-size: 12px; color:#5f5f5f; margin-left: 180px; margin-bottom: 3px;'>сгенерировать URL</p>
</a>
<br />

<div id='buffer' style='display:none;'></div>


<script>
    {literal}
    Object.extend(window, {
        doGenerateUrl: function(res, source)
        {
            var ajax = new Ajax.Updater(
                    "buffer",
                    "{/literal}{$fvConfig->get('dir_web_root')}transliterate/generateurl{literal}",
                    {
                        parameters : {name: $(source).value},
                        asynchronous:true,
                        onComplete: function () {
                            $(res).value = $("buffer").innerHTML;
                        }
                    }
            );
        }
    });

    {/literal}
</script>