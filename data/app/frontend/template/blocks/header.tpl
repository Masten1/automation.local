<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
        <ul class="nav navbar-nav navbar-left">
            <li {if $currentPage->moduleStr eq "tourists"}class="active"{/if} ><a href="/tourists">Туристы</a></li>
            <li {if $currentPage->moduleStr eq "orders"}class="active"{/if}><a href="/orders">Заказы</a></li>
            <li {if $currentPage->moduleStr eq "tours"}class="active"{/if}><a href="/tours">Туры</a></li>
            <li {if $currentPage->moduleStr eq "departure"}class="active"{/if}><a href="/departure">Отправки</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><p class="navbar-text">Пользователь: {$fvUser->name->get()}</p></li>
            <li><button id="logout" class="btn navbar-btn btn-info">Выход</button></li>
        </ul>
    </div>
</nav>