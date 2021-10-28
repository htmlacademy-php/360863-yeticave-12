CREATE DATABASE IF NOT EXISTS yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_title CHAR(63) NOT NULL UNIQUE,
    category_symbolic_code CHAR(63) NOT NULL UNIQUE,
    INDEX index_category_title (category_title)
);

CREATE TABLE IF NOT EXISTS lot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lot_date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lot_title CHAR(127) NOT NULL,
    lot_description CHAR(255) NOT NULL,
    lot_img CHAR(255) NOT NULL UNIQUE,
    lot_starting_price INT NOT NULL,
    lot_completion_date DATETIME NOT NULL,
    lot_bid_step INT NOT NULL,
    lot_author_id INT,
    lot_winner_id INT,
    lot_category_id INT,
    FOREIGN KEY (lot_author_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (lot_winner_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (lot_category_id) REFERENCES category(id) ON DELETE CASCADE,
    INDEX index_lot_title (lot_title),
    INDEX index_lot_description (lot_description)
);


CREATE TABLE IF NOT EXISTS bid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bid_date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bid_sum INT NOT NULL,
    bid_person_id INT,
    bid_lot_id INT,
    FOREIGN KEY (bid_person_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (bid_lot_id) REFERENCES lot(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS person (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    person_email CHAR(63) NOT NULL UNIQUE,
    person_name CHAR(63) NOT NULL,
    person_password CHAR(63) NOT NULL,
    person_contacts CHAR(63) NOT NULL,
    person_lot_id INT,
    person_bid_id INT,
    FOREIGN KEY (person_lot_id) REFERENCES lot(id) ON DELETE CASCADE,
    FOREIGN KEY (person_bid_id) REFERENCES bid(id) ON DELETE CASCADE
);