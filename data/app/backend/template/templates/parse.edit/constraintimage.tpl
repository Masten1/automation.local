
<div id="upload-image"></div>
<div id="upload-result"></div>

<script>
    {literal}
        jQuery(function($){

            var Temp = "{/literal}{$field->itpl->getTemporalPath()}{literal}";

            $("#upload-image").uploadify({
                uploader  : '/js/uploader/uploadify.swf',
                script    : '/js/uploader/uploadify.php',
                cancelImg : '/js/uploader/cancel.png',
                fileExt   : {/literal}"{$field->itpl->acceptedTypes}"{literal},
                fileDesc  : {/literal}"Extensions: {$field->itpl->acceptedTypes}"{literal},
                folder    : Temp,
                multi     : true,
                auto      : true,
                onSelect: function()
                {
                    window.blockScreen();
                },
                onComplete: function( event, ID, fileObj, response, data )
                {
                    $("#contentblocker").fadeOut(300);
                    var fieldHtml = ("<r><![CDATA[{/literal}{$field->generateForeignEntity()|parse:edit}{literal}]]></r>").toString();

                    $("#upload-result").append(fieldHtml);
                }
            });

        });

    {/literal}
</script>