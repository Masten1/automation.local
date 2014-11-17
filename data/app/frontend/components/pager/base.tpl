{if $this->hasPaginate()}
    <div class="pages">
        <div class="pages1">
            <div class="pages2">
                {if $this->getCurrentPage() > 0}
                    {assign var=previousPage value=$this->getCurrentPage()-1}
                    <p><a href="{$this->getPageHref($previousPage)}" rel="{$previousPage}"><img src="/images/left.png" />{"Назад"|translate:"previousPage"}</a></p>
                {/if}

                <ul>
                    {foreach from=$this->getPagesLinks() item=page}
                        {if $lastPage && ($page-$lastPage)>1}
                            <li><a>...</a></li>
                        {/if}

                        {if $page==$this->getCurrentPage()}
                            <li class="pactiv"><a>{$page+1}</a></li>
                        {else}
                            <li><a href="{$this->getPageHref($page)}" rel="{$page}">{$page+1}</a></li>
                        {/if}

                        {assign var="lastPage" value=$page}
                    {/foreach}
                </ul>

                {if $this->getCurrentPage() < $this->getPageCount()-1}
                    {assign var = nextPage value = $this->getCurrentPage()+1}
                    <p><a href="{$this->getPageHref($nextPage)}" rel="{$nextPage}">{"Вперёд"|translate:"nextPage"}<img src="/images/right.png" /></a></p>
                {/if}
            </div>
        </div>
    </div>
{/if}
