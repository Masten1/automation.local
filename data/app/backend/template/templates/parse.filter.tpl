{if $type == 'simple'}
    <div class="field-box">
        <div>
            <form id="search">
                <input type="text" value="{$search}" style="width: 200px"/>
                <button type="submit" class="small">Фильтровать</button> <a class="ajax" href="#/backend/{$smarty.request.__url}">Сбросить</a>
            </form>
        </div>
    </div>

    <script>{literal}
    jQuery(function($){
        $("#search").submit(function(){
            window.location.hash = window.location.hash.toString().replace(/\?.*/,'') + '?search=' + $(this).children('input').val()
                return false;
        });
    });
    {/literal}</script>
{else}
{/if}