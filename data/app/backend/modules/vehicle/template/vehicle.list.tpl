{if !$ajax}
    <h1>{$fvConfig->getModuleName($path)}</h1>

{*parseFilter entity=$class *}

{$collection|parse:filter:$filterConfig}

<div id="result">
    {/if}

    {$collection|parse:table}

    {if !$ajax}
</div>
{/if}

<script>
    {literal}
    Object.extend(window, {

        doSendForm: function ()
        {
            if($('clear').value == 1)
            {
                $('filter').reset();
            }
            $('page').value = 0;
            window.blockScreen();
            var data = Form.serialize("filter") + "&ajax=1";
            new Ajax.Updater(
                    "result",
                    "{/literal}{$fvConfig->get('dir_web_root')}{$path}/{$action}{literal}",
                    {
                        parameters: data,
                        onComplete: function(transport){ window.completeRequest(transport); window.parseForms(); jQuery( "#zebra" ).zebra(); },
                    });
        },
        doPager: function (page)
        {
            var hash = window.location.hash.toString();
            if( hash.match(/\?.*page=/) )
                window.location.hash = hash.replace(/page=\d+/, 'page=' + page);
            else if( hash.match(/\?/) )
                window.location.hash = hash.replace(/&$/, '') + '&page=' + page;
            else
                window.location.hash = hash + '?page=' + page;
            /*
             /*if($('clear').value == 1)
             {
             $('filter').reset();
             }
             $('page').value = page;
             var data = Form.serialize("filter")+ "&ajax=1";
             window.blockScreen();
             var data = "page="+page+"&ajax=1&search=123";
             new Ajax.Updater(
             "result",
             "{/literal}{$fvConfig->get('dir_web_root')}{$path}/{$action}{literal}",
         {
         parameters: data,
         onComplete: function(transport){window.completeRequest(transport);window.parseForms();  jQuery( "#zebra" ).zebra(); },
         });*/
        },
        doSort: function (field, direct)
        {
            if($('clear').value == 1)
            {
                $('filter').reset();
            }

            window.blockScreen();
            $('direct').value = direct;
            $('field').value = field;
            var data = Form.serialize("filter")+ "&ajax=1";
            new Ajax.Updater(
                    "result",
                    "{/literal}{$fvConfig->get('dir_web_root')}{$path}/{$action}{literal}",
                    {
                        parameters: data,
                        onComplete: function(transport){window.completeRequest(transport);window.parseForms();  jQuery( "#zebra" ).zebra(); },
                    });
        }
    });
    {/literal}
</script>
