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
    <div class="container">
        <section class="lots">
            <h2><?=$searchResult; ?></h2>
            <ul class="lots__list">
                <?php foreach ($searchAds as $searchAd): ?>
                    <li class="lots__item lot" >
                        <div class="lot__image">
                            <img src="<?=$searchAd['img']; ?>" width="350" height="260" alt="">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?=$searchAd['category']; ?></span>
                            <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=$searchAd['lotId']; ?>"><?=$searchAd['title']; ?></a></h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= $searchAd['starting_price']; ?></span>
                                </div>
                                <div class="lot__timer timer <?= ($searchAd['timeLeft']["hoursLeft"] === '00') ? 'timer--finishing': ''; ?>">
                                    <?=$searchAd['timeLeft']["hoursLeft"] . ':' . $searchAd['timeLeft']["minutesLeft"];?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="../img/lot-1.jpg" width="350" height="260" alt="Сноуборд">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category">Доски и лыжи</span>
                        <h3 class="lot__title"><a class="text-link" href="lot.html">2014 Rossignol District Snowboard</a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost">10 999<b class="rub">р</b></span>
                            </div>
                            <div class="lot__timer timer">
                                16:54:12
                            </div>
                        </div>
                    </div>
                </li>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="../img/lot-2.jpg" width="350" height="260" alt="Сноуборд">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category">Доски и лыжи</span>
                        <h3 class="lot__title"><a class="text-link" href="lot.html">DC Ply Mens 2016/2017 Snowboard</a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">12 ставок</span>
                                <span class="lot__cost">15 999<b class="rub">р</b></span>
                            </div>
                            <div class="lot__timer timer timer--finishing">
                                00:54:12
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </section>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
            <li class="pagination-item pagination-item-active"><a>1</a></li>
            <li class="pagination-item"><a href="#">2</a></li>
            <li class="pagination-item"><a href="#">3</a></li>
            <li class="pagination-item"><a href="#">4</a></li>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        </ul>
    </div>
</main>