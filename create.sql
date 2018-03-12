DROP DATABASE IF EXISTS bit_test;
CREATE DATABASE bit_test CHARACTER SET utf8 COLLATE utf8_general_ci;
USE bit_test;

CREATE TABLE IF NOT EXISTS users(
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name varchar(256)   NOT NULL,
    last_name  varchar(32)    NOT NULL,
    email      varchar(255)   NOT NULL,
    password   varchar(128)   NOT NULL,
    balance    decimal(10, 2) NOT NULL)
    ENGINE=InnoDB
    COMMENT="Users";

CREATE TABLE IF NOT EXISTS balance_history(
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id int(11) NOT NULL,
    balance_before decimal(10, 2) NOT NULL,
    write_off_amount decimal(10, 2) NOT NULL)
    ENGINE=InnoDB
    COMMENT="Balance history";

CREATE INDEX balance_history_user_id ON balance_history(user_id);

CREATE USER 'super_admin'@'localhost'  IDENTIFIED BY '#superadmin2018#';
GRANT SELECT,INSERT,UPDATE,DELETE ON bit_test.* TO 'super_admin'@'localhost';

INSERT INTO bit_test.users(id, first_name, last_name, email, password, balance) values(0, "bit", "test", "test@example.com", "098f6bcd4621d373cade4e832627b4f6", 1000000);
