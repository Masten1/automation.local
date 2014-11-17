<label>{$field->getName()} <a href="{$field}" id="href-{$field->getToken()}" target="_new" style="border-bottom: 1px dashed #666; color:#666;">проcмотреть файл</a></label>

<div style="float: left;">
    <input id="{$field->getToken()}" name="file_upload" type="file" style="float: left;" />
    <input type="hidden" name="{$arrayName}[{$name}]" value="{ $field->get()}" id="val-{$field->getToken()}" style="float: left;">
</div>

<br clear="all"/>

<script>
    {literal}
    jQuery(function($) 
    {
        var o = $('#{/literal}{$field->getToken()}{literal}');
        var r = $('#{/literal}val-{$field->getToken()}{literal}');
        var h = $('#{/literal}href-{$field->getToken()}{literal}');
        
        if( ! h.attr("href").length )
            h.hide();
        
        var uploadPath = "{/literal}{$field->getTemporalPath()}{literal}";    

        o.uploadify({
            uploader  : '/js/uploader/uploadify.swf',
            script    : '/js/uploader/uploadify.php',
            cancelImg : '/js/uploader/cancel.png',
            fileExt   : {/literal}"{$field->acceptedTypes}"{literal},
            fileDesc  : {/literal}"Extensions: {$field->acceptedTypes}"{literal},
            folder    : uploadPath,
            multi     : false,
            auto      : true,
            onSelect: function()
            {
                window.blockScreen();    
            },
            onComplete: function( event, ID, fileObj, response, data )
            {
                $("#contentblocker").fadeOut(300);
                h.attr("href", uploadPath + response).fadeIn();
                r.val(response);
            }
        });
    });
    {/literal}
</script>
