<aside class="left-menu static">
    {show_module module="menu" view="side"}
</aside>

<section id="static-content">
    <div>
        <div class="static-head">{$page->name->get()}</div>
        {show_module module="categories" view="top"}
    </div>
    {show_module module="breadcrumbs" view="index"}
    <article>
        {$page->text}
    </article>
</section>

<img class="index-img" src="/images/index.png" alt="NewLife" />

