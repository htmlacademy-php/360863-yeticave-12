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
    <section class="lot-item container">
        <h2><?= $lot['title']; ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= $lot['img']; ?>" width="730" height="548" alt="Сноуборд">
                </div>
                <p class="lot-item__category">Категория: <span><?= $lot['category']; ?></span></p>
                <p class="lot-item__description"><?= $lot['description']; ?></p>
            </div>
            <div class="lot-item__right">
                    <div class="lot-item__state">
                        <div class="lot-item__timer timer <?= ($lot['timeLeft']["hoursLeft"] === '00') ? 'timer--finishing' : ''; ?>">
                            <?= $lot['timeLeft']["hoursLeft"] . ':' . $lot['timeLeft']["minutesLeft"]; ?>
                        </div>
                        <div class="lot-item__cost-state">
                            <div class="lot-item__rate">
                                <span class="lot-item__amount">Текущая цена</span>
                                <span class="lot-item__cost"><?= $lot['price']; ?></span>
                            </div>
                            <div class="lot-item__min-cost">
                                Мин. ставка <span><?= $lot['bid_step']; ?></span>
                            </div>
                        </div>
                        <?php if ($isTakeBidsVisible): ?>
                        <form class="lot-item__form" action="/lot.php?id=<?= $lot['id']; ?>" method="post"
                              autocomplete="off">
                            <p class="lot-item__form-item form__item <?= (!empty($errors['cost'])) ? 'form__item--invalid' : '' ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="cost"
                                       placeholder="12 000" <?= (!empty($safeData['cost'])) ? 'value =' . $safeData['cost'] : '' ?> >
                                <?php if (!empty($errors['cost'])): ?>
                                    <span class="form__error"><?= $errors['cost']; ?></span>
                                <?php endif; ?>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                        <?php endif; ?>
                    </div>
                <div class="history">
                    <h3>История ставок (<span><?= count($bids); ?></span>)</h3>
                    <?php if (count($bids) > 0): ?>
                        <table class="history__list">
                            <?php foreach ($bids as $bid): ?>
                                <tr class="history__item">
                                    <td class="history__name"><?= $bid['name']; ?></td>
                                    <td class="history__price"><?= $bid['sum']; ?></td>
                                    <td class="history__time"><?= $bid['time_passed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>