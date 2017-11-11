-- -------------
-- Enjin Coin SDK MySQL DB Schema

CREATE DATABASE enjin_coin;

CREATE TABLE db_info (
  `db_version` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE apps (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE transaction_requests (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `app_id` INT(10) unsigned NOT NULL,
  `identity_id` INT(10) unsigned NOT NULL,
  `type` ENUM('buy', 'sell', 'send', 'use', 'subscribe') NOT NULL DEFAULT 'send',
  `recipient_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `recipient_address` VARCHAR(42) NULL DEFAULT NULL,
  `icon` VARCHAR(255) NULL DEFAULT NULL,
  `title` VARCHAR(255) NULL DEFAULT NULL,
  `token_id` INT(10) UNSIGNED NOT NULL,
  `value` VARCHAR(255) NOT NULL DEFAULT '0',
  `accepted` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`app_id`), -- Review keys and optimize later
  KEY (`identity_id`),
  KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------
-- Events

CREATE TABLE events (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `app_id` INT(10) unsigned NOT NULL,
  -- `identity_id` INT(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- -------------
-- Identities

DROP TABLE identities;
CREATE TABLE identities (
  `identity_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `ethereum_address` VARCHAR(255) NULL DEFAULT NULL,
  `linking_code` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`identity_id`),
  UNIQUE KEY (`linking_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*
DROP TABLE identity_types;
CREATE TABLE identity_types (
  `type_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
DROP TABLE identity_fields;
CREATE TABLE identity_fields (
  `field_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  /*`type_id` INT(10) unsigned NOT NULL DEFAULT 0,*/
  `key` VARCHAR(255) NULL DEFAULT NULL,
  `searchable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `displayable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `unique` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE identity_values;
CREATE TABLE identity_values (
  `identity_id` INT(10) unsigned NOT NULL,
  `field_id` INT(10) unsigned NOT NULL,
  `value` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`value`, `field_id`, `identity_id`),
  KEY (`identity_id`),
  KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE identity_values ADD CONSTRAINT `fk_identity_values` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`identity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- -------------
-- Tokens

CREATE TABLE tokens (
  `app_id` INT(10) unsigned NOT NULL,
  `token_id` INT(10) unsigned NOT NULL,
  `decimals` TINYINT(2) unsigned NOT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
