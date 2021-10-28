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
    lot_author_id INT NOT NULL,
    lot_winner_id INT NOT NULL,
    lot_category_id INT NOT NULL,
    FOREIGN KEY (lot_author_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (lot_winner_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (lot_category_id) REFERENCES category(id) ON DELETE CASCADE,
    INDEX index_lot_title (lot_title),
    INDEX index_lot_description (lot_description)
);


CREATE TABLE IF NOT EXISTS bid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bid_date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bid_sum INT NOT NULL,
    bid_user_id INT NOT NULL,
    bid_lot_id INT NOT NULL,
    FOREIGN KEY (bid_user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (bid_lot_id) REFERENCES lot(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_date_created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_email CHAR(63) NOT NULL UNIQUE,
    user_name CHAR(63) NOT NULL,
    user_password CHAR(63) NOT NULL,
    user_contacts CHAR(63) NOT NULL,
    user_lot_id INT NOT NULL,
    user_bid_id INT NOT NULL,
    FOREIGN KEY (user_lot_id) REFERENCES lot(id) ON DELETE CASCADE,
    FOREIGN KEY (user_bid_id) REFERENCES bid(id) ON DELETE CASCADE
);