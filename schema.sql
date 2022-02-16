CREATE DATABASE IF NOT EXISTS yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(63) NOT NULL UNIQUE,
    symbolic_code VARCHAR(63) NOT NULL UNIQUE,
    INDEX index_category_title (title)
);

CREATE TABLE IF NOT EXISTS lot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(127) NOT NULL,
    description VARCHAR(255) NOT NULL,
    img VARCHAR(255) NOT NULL UNIQUE,
    starting_price INT NOT NULL,
    completion_date DATETIME NOT NULL,
    bid_step INT NOT NULL,
    author_id INT,
    winner_id INT,
    category_id INT,
    INDEX index_lot_title (title),
    INDEX index_lot_description (description)
);


CREATE TABLE IF NOT EXISTS bid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    sum INT NOT NULL,
    person_id INT,
    lot_id INT
);

CREATE TABLE IF NOT EXISTS person (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(63) NOT NULL UNIQUE,
    name VARCHAR(63) NOT NULL,
    password VARCHAR(63) NOT NULL,
    contacts VARCHAR(63) NOT NULL
);

ALTER TABLE lot
    ADD (FOREIGN KEY (author_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE);

ALTER TABLE bid
    ADD (FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (lot_id) REFERENCES lot(id) ON DELETE CASCADE);

CREATE FULLTEXT INDEX lot_ft_search ON lot (title, description);