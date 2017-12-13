-- -------------
-- Enjin Coin SDK MySQL DB Schema

CREATE DATABASE IF NOT EXISTS enjin_coin;

USE enjin_coin;

DROP TABLE IF EXISTS db_info;
CREATE TABLE db_info (
  `db_version` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS apps;
CREATE TABLE apps (
  `app_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `app_auth_key` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS transaction_requests;
CREATE TABLE transaction_requests (
  `txr_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tx_id` VARCHAR(255) NULL DEFAULT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `app_id` INT(10) UNSIGNED NOT NULL,
  `identity_id` INT(10) UNSIGNED NOT NULL,
  `type` ENUM('buy', 'sell', 'send', 'use', 'subscribe') NOT NULL DEFAULT 'send',
  `recipient_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `recipient_address` VARCHAR(42) NULL DEFAULT NULL,
  `icon` VARCHAR(255) NULL DEFAULT NULL,
  `title` VARCHAR(255) NULL DEFAULT NULL,
  `token_id` INT(10) UNSIGNED NOT NULL,
  `value` VARCHAR(255) NOT NULL DEFAULT '0',
  `state` ENUM('pending', 'broadcasted', 'executed', 'confirmed', 'canceled_user', 'canceled_platform', 'failed') NOT NULL DEFAULT 'pending',
  `accepted` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`txr_id`),
  KEY (`app_id`), -- Review keys and optimize later
  KEY (`tx_id`),
  KEY (`identity_id`),
  KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- -------------
-- Events

DROP TABLE IF EXISTS events;
CREATE TABLE events (
  `event_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `app_id` INT(10) UNSIGNED NOT NULL,
  `identity_id` INT(10) UNSIGNED NOT NULL,
  `event_type` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `data` VARCHAR(8191) NULL DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- -------------
-- Identities

DROP TABLE IF EXISTS identity_fields;
CREATE TABLE identity_fields (
  `field_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` INT(10) UNSIGNED NOT NULL,
  `key` VARCHAR(255) NULL DEFAULT NULL,
  `searchable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `displayable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `unique` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS identity_values;
CREATE TABLE identity_values (
  `identity_id` INT(10) UNSIGNED NOT NULL,
  `field_id` INT(10) UNSIGNED NOT NULL,
  `value` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`value`, `field_id`, `identity_id`),
  KEY (`identity_id`),
  KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS identities;
CREATE TABLE identities (
  `identity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ethereum_address` VARCHAR(255) NULL DEFAULT NULL,
  `identity_code` VARCHAR(255) NULL DEFAULT NULL,
  `auth_key` VARCHAR(255) NULL DEFAULT NULL,
  `role` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`identity_id`),
  KEY (`identity_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE identity_values ADD CONSTRAINT `fk_identity_values` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`identity_id`) ON DELETE CASCADE ON UPDATE CASCADE;



-- -------------
-- Tokens

DROP TABLE IF EXISTS tokens;
CREATE TABLE tokens (
  `app_id` INT(10) UNSIGNED NOT NULL,
  `token_id` INT(10) UNSIGNED NOT NULL,
  `decimals` TINYINT(2) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- -------------
-- Prices

DROP TABLE IF EXISTS prices;
CREATE TABLE prices (
  `timestamp` TIMESTAMP NOT NULL,
  `value` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;