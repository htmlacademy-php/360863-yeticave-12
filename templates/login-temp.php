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
    <form class="form container" action="/login.php" method="post"> <!-- form--invalid -->
        <h2>Вход</h2>
        <div class="form__item <?php if (!empty($errors['email']) || !empty($errorsLogin['email'])) {
            echo 'form__item--invalid';
        } ?>"> <!-- form__item--invalid -->
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail"
                   value="<?php if (!empty($safeData['email'])) {
                       echo $safeData['email'];
                   } ?>">
            <?php if (!empty($errors['email'])): ?>
                <span class="form__error"><?= $errors['email']; ?></span>
            <?php elseif (!empty($errorsLogin['email'])): ?>
                <span class="form__error"><?= $errorsLogin['email']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item form__item--last <?php if (!empty($errors['password']) || !empty($errorsLogin['password'])) {
            echo 'form__item--invalid';
        } ?>"> <!-- form__item--invalid -->
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль">
            <?php if (!empty($errors['password'])): ?>
                <span class="form__error"><?= $errors['password']; ?></span>
            <?php elseif (!empty($errorsLogin['password'])): ?>
                <span class="form__error"><?= $errorsLogin['password']; ?></span>
            <?php endif; ?>
        </div>
        <button type="submit" class="button">Войти</button>
    </form>
</main>