<main>

    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/category.php?category=<?= $category['symbolic_code']; ?>"><?= $category['title']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <section class="rates container">
        <h2>Мои ставки</h2>
        <table class="rates__list">
            <?php foreach ($userBids as $userBid): ?>
                <tr class="rates__item">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="<?= $userBid['img']; ?>" width="54" height="40" alt="Сноуборд">
                        </div>
                        <div>
                            <h3 class="rates__title"><a
                                        href="/lot.php?id=<?= $userBid['lotId']; ?>"><?= $userBid['title']; ?></a></h3>
                            <?php if (!empty($userBid['userContacts'])): ?>
                                <p><?= $userBid['userContacts']; ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="rates__category">
                        <?= $userBid['categoryTitle']; ?>
                    </td>
                    <td class="rates__timer">
                        <div class="timer <?= $userBid['timerClass']; ?>"><?= $userBid['timerText']; ?></div>
                    </td>
                    <td class="rates__price">
                        <?= $userBid['price'] ?> р
                    </td>
                    <td class="rates__time">
                        <?= $userBid['time_passed']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>