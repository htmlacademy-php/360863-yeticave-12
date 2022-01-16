<main>
<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/category.php?category=<?=$category['symbolic_code']; ?>"><?=htmlspecialchars($category['title']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form form--add-lot container form--invalid" action="../add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?php if(!empty($errors['lot-name'])) { echo 'form__item--invalid';} ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?php if(!empty($safeData['lot-name'])) { echo $safeData['lot-name'];} ?>">
            <?php if(!empty($errors['lot-name'])): ?>
            <span class="form__error"><?=$errors['lot-name']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item <?php if(!empty($errors['category'])) { echo 'form__item--invalid';} ?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">
                <option>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?=$category['id']; ?>" <?=$category['sectionClass']; ?>><?=$category['title']; ?></option>
                <?php endforeach;?>
            </select>
            <?php if(!empty($errors['category'])): ?>
                <span class="form__error"><?=$errors['category']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="form__item form__item--wide <?php if(!empty($errors['message'])) { echo 'form__item--invalid';} ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?php if(!empty($safeData['message'])) { echo $safeData['message'];} ?></textarea>
        <?php if(!empty($errors['message'])): ?>
            <span class="form__error"><?=$errors['message']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item form__item--file <?php if(!empty($errors['lot-img'])) { echo 'form__item--invalid';} ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="lot-img"id="lot-img" value="">
            <input type="hidden" name="img" value="<?php if(!empty($imgValue)) { echo $imgValue;} ?>" />
            <label for="lot-img">
                Добавить
            </label>
        </div>
        <?php if(!empty($errors['lot-img'])): ?>
            <span class="form__error"><?=$errors['lot-img']; ?></span>
        <?php endif; ?>
    </div>

    <div class="form__container-three">
        <div class="form__item form__item--small <?php if(!empty($errors['lot-rate'])) { echo 'form__item--invalid';} ?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?php if(!empty($safeData['lot-rate'])) { echo $safeData['lot-rate'];} ?>">
            <?php if(!empty($errors['lot-rate'])): ?>
                <span class="form__error"><?=$errors['lot-rate']; ?></span>
            <?php endif; ?>

        </div>
        <div class="form__item form__item--small <?php if(!empty($errors['lot-step'])) { echo 'form__item--invalid';} ?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?php if(!empty($safeData['lot-step'])) { echo $safeData['lot-step'];} ?>">
            <?php if(!empty($errors['lot-step'])): ?>
                <span class="form__error"><?=$errors['lot-step']; ?></span>
            <?php endif; ?>

        </div>
        <div class="form__item <?php if(!empty($errors['lot-date'])) { echo 'form__item--invalid';} ?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?php if(!empty($safeData['lot-date'])) { echo $safeData['lot-date'];} ?>">
            <?php if(!empty($errors['lot-date'])): ?>
                <span class="form__error"><?=$errors['lot-date']; ?></span>
            <?php endif; ?>

        </div>
    </div>

    <?php if(count($errors) > 0): ?>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif; ?>

    <button type="submit" name="send" class="button">Добавить лот</button>
</form>
</main>