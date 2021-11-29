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
<form class="form form--add-lot container form--invalid" action="../add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?=$errors['lot-name'] ? 'form__item--invalid' : ''?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=$valuesLotForm['lot-name']; ?>">
            <?php if($errors['lot-name']): ?>
            <span class="form__error"><?=$errors['lot-name']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item <?=$errors['category'] ? 'form__item--invalid' : ''?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">
                <option>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?=$category['id']; ?>" <?=$category['isSelected']; ?>><?=$category['title']; ?></option>
                <?php endforeach;?>
            </select>
            <?php if($errors['category']): ?>
                <span class="form__error"><?=$errors['category']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="form__item form__item--wide <?=$errors['message'] ? 'form__item--invalid' : ''?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?=$valuesLotForm['message']; ?></textarea>
        <?php if($errors['message']): ?>
            <span class="form__error"><?=$errors['message']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item form__item--file <?=$errors['lot-img'] ? 'form__item--invalid' : ''?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="lot-img" id="lot-img" value="<?=$valuesLotForm['lot-img']; ?>">
            <label for="lot-img">
                Добавить
            </label>
        </div>
        <?php if($errors['lot-img']): ?>
            <span class="form__error"><?=$errors['lot-img']; ?></span>
        <?php endif; ?>
    </div>

    <div class="form__container-three">
        <div class="form__item form__item--small <?=$errors['lot-rate'] ? 'form__item--invalid' : ''?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?=$valuesLotForm['lot-rate']; ?>">
            <?php if($errors['lot-rate']): ?>
                <span class="form__error"><?=$errors['lot-rate']; ?></span>
            <?php endif; ?>

        </div>
        <div class="form__item form__item--small <?=$errors['lot-step'] ? 'form__item--invalid' : ''?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?=$valuesLotForm['lot-step']; ?>">
            <?php if($errors['lot-step']): ?>
                <span class="form__error"><?=$errors['lot-step']; ?></span>
            <?php endif; ?>

        </div>
        <div class="form__item <?=$errors['lot-date'] ? 'form__item--invalid' : ''?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?=$valuesLotForm['lot-date']; ?>">
            <?php if($errors['lot-date']): ?>
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