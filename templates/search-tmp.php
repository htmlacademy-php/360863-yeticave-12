<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/category.php?category=<?= $category['symbolic_code']; ?>"><?= htmlspecialchars($category['title']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="container">
        <section class="lots">
            <h2><?= $searchResult; ?></h2>
            <ul class="lots__list">
                <?php foreach ($searchAds as $searchAd): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?= $searchAd['img']; ?>" width="350" height="260" alt="">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?= $searchAd['category']; ?></span>
                            <h3 class="lot__title"><a class="text-link"
                                                      href="/lot.php?id=<?= $searchAd['id']; ?>"><?= $searchAd['title']; ?></a>
                            </h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <?php if ($searchAd['bid_sum'] == 0): ?>
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost"><?= $searchAd['starting_price']; ?></span>
                                    <?php else: ?>
                                        <span class="lot__amount"><?= $searchAd['bid_sum']; ?> ставок</span>
                                        <span class="lot__cost"><?= $searchAd['current_price']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="lot__timer timer <?= ($searchAd['timeLeft']["hoursLeft"] === '00') ? 'timer--finishing' : ''; ?>">
                                    <?= $searchAd['timerText']; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php if ($pagination['pagesCount'] > 1): ?>

            <ul class="pagination-list">
                <?php if ($pagination['isFirstPageExist']): ?>
                    <li class="pagination-item pagination-item-prev"><a
                                href="/search.php?page=<?= ($pagination['curPage'] - 1); ?>&search=<?= $safeDataSearch; ?>&find=Найти">Назад</a>
                    </li>
                <?php endif; ?>
                <?php foreach ($pagination['pages'] as $page): ?>
                    <li class="pagination-item <?php if ($page == $pagination['curPage']): ?>pagination__item--active<?php endif; ?>">
                        <a href="/search.php?page=<?= $page; ?>&search=<?= $safeDataSearch; ?>"><?= $page; ?></a>
                    </li>
                <?php endforeach; ?>
                <?php if ($pagination['isLastPageExist']): ?>
                    <li class="pagination-item pagination-item-next"><a
                                href="/search.php?page=<?= ($pagination['curPage'] + 1); ?>&search=<?= $safeDataSearch; ?>&find=Найти">Вперед</a>
                    </li>
                <?php endif; ?>
            </ul>

        <?php endif; ?>
    </div>
</main>