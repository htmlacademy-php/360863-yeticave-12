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
    <form class="form container form--invalid" action="/sign.php" method="post" autocomplete="off"> <!-- form
    --invalid -->
        <h2>Регистрация нового аккаунта</h2>
        <div class="form__item <?php if(!empty($errors['email'])) { echo 'form__item--invalid';} ?>"> <!-- form__item--invalid -->
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?php if(!empty($safeData['email'])) { echo $safeData['email'];} ?>">
            <?php if(!empty($errors['email'])): ?>
                <span class="form__error"><?=$errors['email']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item <?php if(!empty($errors['password'])) { echo 'form__item--invalid';} ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?php if(!empty($safeData['password'])) { echo $safeData['password'];} ?>">
            <?php if(!empty($errors['password'])): ?>
                <span class="form__error">Введите пароль</span>
            <?php endif; ?>
        </div>
        <div class="form__item <?php if(!empty($errors['name'])) { echo 'form__item--invalid';} ?>">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="name" placeholder="Введите имя" value="<?php if(!empty($safeData['name'])) { echo $safeData['name'];} ?>">
            <?php if(!empty($errors['name'])): ?>
                <span class="form__error">Введите имя</span>
            <?php endif; ?>
        </div>
        <div class="form__item <?php if(!empty($errors['message'])) { echo 'form__item--invalid';} ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?php if(!empty($safeData['message'])) { echo $safeData['message'];} ?></textarea>
            <?php if(!empty($errors['name'])): ?>
                <span class="form__error">Напишите как с вами связаться</span>
            <?php endif; ?>
        </div>
        <?php if(count($errors) > 0): ?>
            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
        <?php endif; ?>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>
</main>