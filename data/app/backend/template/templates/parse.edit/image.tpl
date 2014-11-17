<label style="width: 100%;">{$field->getName()} <a href="javascript:void(0);" path="{$field}" id="href-{$field->getToken()}" style="border-bottom: 1px dashed #666; color:#666;">проcмотреть файл</a></label>

<div id="prev-{$field->getToken()}" style="display: none;">
    <img src="{$field}">

    <a href="javascript:void(0);" class="resize" style="display: none;">Применить</a>
</div>

<div style="float: left;margin-top: 10px">
    <input id="{$field->getToken()}" name="file_upload" type="file" style="float: left;" />
    <input type="hidden" name="{$arrayName}[{$name}]" value="{$field->get()}" id="val-{$field->getToken()}" style="float: left;">
    {*<a href="javascript:void(null);">удалить</a>*}
</div>

<br clear="all"/>

<script>
    {literal}
    jQuery(function($)
    {
        var o = $('#{/literal}{$field->getToken()}{literal}');
        var p = $('#{/literal}prev-{$field->getToken()}{literal}');
        var r = $('#{/literal}val-{$field->getToken()}{literal}');
        var h = $('#{/literal}href-{$field->getToken()}{literal}');


        h.click(function(){
            var wrap = p.splash({
                            close: 0,
                            width: 'auto'
                            });
            var cropApi;
            var prop = {
                        boxWidth: 500,
                        boxHeight: 300,
                        {/literal}
                        {if $field->getAspectRatio()}
                        aspectRatio: {$field->getAspectRatio()},
                        {/if}
                        {literal}
                        onSelect: function( coords ){
                                wrap.find("img").eq(0).data({crop: coords});
                                wrap.find("a.resize").fadeIn();
                            }
                        };

            var img = wrap.find("img").eq(0).Jcrop(prop,function(){
                            cropApi = this;
                        });

            wrap.find("a.resize").click(function(){
                        $.ajax({
                            url: "/backend/{/literal}{$smarty.request.module}{literal}/resize/",
                            type: "post",
                            data: {
                                    path: h.attr("path"),
                                    params: wrap.find("img").eq(0).data("crop")
                                },
                            success: function(){
                                    var path = wrap.find("img").eq(0).attr("src") + "?" + Math.random().toString().replace(".","_");

                                    cropApi.destroy();
                                    img.hide().attr("src",path).Jcrop(prop,function(){
                                                                            cropApi = this;
                                                                        }).show();
                                }
                            });
                    });
        });

        if( ! p.find("img").eq(0).attr("src").length )
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
                h.attr("path", uploadPath + response).fadeIn();
                p.find("img").eq(0).attr("src", uploadPath + response);
                r.val(response);

                h.click();
            }
        });
    });
    {/literal}
</script>
