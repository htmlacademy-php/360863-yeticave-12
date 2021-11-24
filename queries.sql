USE yeticave;

/*существующий список категорий;*/
INSERT INTO
    category(title, symbolic_code)
VALUES
       ('Доски и лыжи', 'boards'),
       ('Крепления', 'attachment'),
       ('Ботинки', 'boots'),
       ('Одежда', 'clothing'),
       ('Инструменты', 'tools'),
       ('Разное', 'other');

/*придумайте пару пользователей;*/
INSERT INTO
    person(email, name, password, contacts)
VALUES
    ('petr@mail.ru', 'Петя', 'qwerty', 'тел. 89995554433'),
    ('anna@mail.ru', 'Аня', 'qwerty', 'тел. 89996665522'),
    ('makar@mail.ru', 'Макар', 'qwerty', 'тел. 87773337788');

/*существующий список объявлений;*/
INSERT INTO
    lot(title, description, img, starting_price, completion_date, bid_step, author_id, category_id)
VALUES
    ('2014 Rossignol District Snowboard', 'Почувствуйте карверскую мощь. Allspeed 100 обеспечивает высокую мощность и точность, чему способствует плотная колодка. Адаптируемый сапожек с Thinsulate', './img/lot-1.jpg', 10999, '2022-01-28 01:02:03', 100, 1, 1),
    ('DC Ply Mens 2016/2017 Snowboard', 'Традиционный кэмбер (6 мм) в центральной части (зона стойки) и удлиненные плоские зоны соприкосновения в носовой и хвостовой частях – для стабильности и большей передачи силы.', './img/lot-2.jpg', 159999, '2022-01-25 01:02:03', 100, 2, 1),
    ('Крепления Union Contact Pro 2015 года размер L/XL', 'Сноубордические крепления Union Cadet созданы для детей, которые хотят покорить всю гору и освоить все трассы. Отличные крепления для подрастающих райдеров стали еще лучше.', './img/lot-3.jpg', 8000, '2022-01-20 01:02:03', 200, 3, 2),
    ('Ботинки для сноуборда DC Mutiny Charocal', 'Сноубордические ботинки на шнуровке Phase. Простые и лаконичные, но отнюдь не базовые с точки зрения технических характеристик, Phase остаются самыми демократичными сноубордическими ботинками в нашей линейке.', './img/lot-4.jpg', 10999, '2022-01-10 01:02:03', 100, 1, 3),
    ('Куртка для сноуборда DC Mutiny Charocal', 'Сноубордическая куртка Defy выполнена из водонепроницаемой мембраны 10K с использованием технологии Weather Defense.', './img/lot-5.jpg', 7500, '2022-02-15 01:02:03', 100, 2, 4),
    ('Маска Oakley Canopy', 'Маска Line Miner L была создана с целью обеспечить максимальный периферийный обзор при использовании цилиндрической линзы.', './img/lot-6.jpg', 5400, '2022-03-15 01:02:03', 100, 3, 6);

/*добавьте пару ставок для любого объявления.*/
INSERT INTO
    bid(sum, person_id, lot_id)
VALUES
    (8100, 1, 3),
    (8200, 2, 3);

INSERT INTO
    bid(sum, person_id, lot_id)
VALUES
    (8400, 3, 3),
    (8600, 1, 3);

/*получить все категории*/
SELECT title FROM category;

/*получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;*/
SELECT lot.title as title, starting_price, img, category.title as category, MAX(bid.sum)
FROM lot
         JOIN category ON category.id = lot.category_id
         LEFT JOIN bid ON lot.id = bid.lot_id
WHERE completion_date > now()
GROUP BY lot.title, starting_price, img, lot.date_created_at
ORDER BY lot.date_created_at DESC;

/*показать лот по его ID. Получите также название категории, к которой принадлежит лот;*/
SELECT lot.id as lot_id, category.title as category_title
FROM lot
LEFT JOIN category
ON lot.category_id = category.id;

/*обновить название лота по его идентификатору;*/
UPDATE lot SET title = '2015 Rossignol District Snowboard' WHERE id = 1;

/*получить список ставок для лота по его идентификатору с сортировкой по дате.*/
SELECT sum
FROM bid
WHERE lot_id = 3
ORDER BY date_created_at DESC;