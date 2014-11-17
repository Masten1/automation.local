<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="uk">
<head>
    <title>{$currentPage->getTitle()}</title>

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="keywords" content="{$currentPage->getKeyWords()}" />
    <meta name="description" content="{$currentPage->getDescription()}"/>


    {foreach item=Css from=$fvConfig->get('includes.css')}
        <link rel="stylesheet" type="text/css" href="{$Css|static_loader}"/>
    {/foreach}

    {foreach from=$currentPage->getCss() item=file}
        <link rel="stylesheet" type="text/css" href="{$file|static_loader}"/>
    {/foreach}
    {foreach item=Js from=$fvConfig->get('includes.js')}
        <script type="text/javascript" src="{$Js|static_loader}"></script>
    {/foreach}

    {foreach from=$currentPage->getJS() item=file}
        <script type="text/javascript" src="{$file|static_loader}"></script>
    {/foreach}

    <!--[if lte IE 8]>
    <script src="/js/html5.js" ></script>
    <![endif]-->
</head>
<body>
    <div id="wrapper">
        {$currentPage->getPageContentPart("page_content")}
    </div>
</body>
</html>

