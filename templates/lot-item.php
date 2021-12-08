<main>
<nav class="nav">
    <ul class="nav__list container">
        <li class="nav__item">
            <a href="all-lots.html">Доски и лыжи</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Крепления</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Ботинки</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Одежда</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Инструменты</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Разное</a>
        </li>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?=$lot['title']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=$lot['img']; ?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?=$lot['category']; ?></span></p>
            <p class="lot-item__description"><?=$lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <div class="lot-item__timer timer <?= ($lot['timeLeft']["hoursLeft"] === '00') ? 'timer--finishing': ''; ?>">
                    <?=$lot['timeLeft']["hoursLeft"] . ':' . $lot['timeLeft']["minutesLeft"];?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?= $lot['price']; ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=$lot['bid_step']; ?></span>
                    </div>
                </div>
                <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post" autocomplete="off">
                    <p class="lot-item__form-item form__item form__item--invalid">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" placeholder="12 000">
                        <span class="form__error">Введите наименование лота</span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <div class="history">
                <h3>История ставок (<span><?= count($bids); ?></span>)</h3>
                <?php if(count($bids) > 0):?>
                <table class="history__list">
                    <?php foreach ($bids as $bid): ?>
                    <tr class="history__item">
                        <td class="history__name"><?= $bid['name']; ?></td>
                        <td class="history__price"><?= $bid['sum']; ?></td>
                        <td class="history__time"><?= $bid['time_passed']; ?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
    </main>