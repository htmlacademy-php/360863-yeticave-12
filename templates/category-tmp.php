<main>

    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/category.php?category=<?=$category['symbolic_code']; ?>"><?=$category['title']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="container">
        <section class="lots">
            <h2><?=$pageH2; ?></h2>
            <ul class="lots__list">
                <?php foreach ($categoryAds as $categoryAd): ?>
                      <li class="lots__item lot" >
                        <div class="lot__image">
                            <img src="<?=$categoryAd['img']; ?>" width="350" height="260" alt="">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?=$categoryAd['category']; ?></span>
                            <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=$categoryAd['id']; ?>"><?=$categoryAd['title']; ?></a></h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <?php if($categoryAd['bid_sum'] == 0): ?>
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= $categoryAd['starting_price']; ?></span>
                                    <?php else:?>
                                    <span class="lot__amount"><?=$categoryAd['bid_sum']; ?> ставок</span>
                                    <span class="lot__cost"><?= $categoryAd['current_price']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="lot__timer timer <?= ($categoryAd['timeLeft']["hoursLeft"] === '00') ? 'timer--finishing': ''; ?>">
                                    <?=$categoryAd['timeLeft']["hoursLeft"] . ':' . $categoryAd['timeLeft']["minutesLeft"];?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php if ($pages_count > 1): ?>

                <ul class="pagination-list">
                    <?php if ($isFirstPageExist): ?>
                    <li class="pagination-item pagination-item-prev"><a href="/category.php?page=<?=($cur_page - 1) ;?>&category=<?=$safeData['category']; ?>">Назад</a></li>
                    <?php endif; ?>
                    <?php foreach ($pages as $page): ?>
                        <li class="pagination-item <?php if ($page == $cur_page): ?>pagination__item--active<?php endif; ?>">
                            <a href="/category.php?page=<?=$page;?>&category=<?=$safeData['category']; ?>"><?=$page;?></a>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($isLastPageExist): ?>
                    <li class="pagination-item pagination-item-next"><a href="/category.php?page=<?=($cur_page + 1) ;?>&category=<?=$safeData['category']; ?>">Вперед</a></li>
                    <?php endif; ?>
                </ul>

        <?php endif; ?>
    </div>
</main>