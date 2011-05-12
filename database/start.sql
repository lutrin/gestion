/* create table editor */
CREATE TABLE `gestion`.`editor` (
  `k` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `username` VARCHAR( 30 ) NOT NULL ,
  `password` VARCHAR( 255 ) NOT NULL ,
  `admin` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `active` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  INDEX ( `username` )
) ENGINE = MyISAM;

/* insert root editor */
INSERT INTO `gestion`.`editor` (
  `k` ,
  `username` ,
  `password` ,
  `admin` ,
  `active`
)
VALUES (
  NULL , 'root', PASSWORD( 'd033e22ae348aeb5660fc2140aec35850c4da997' ) , '1', '1'
);

/* add longname field */
ALTER TABLE `editor` ADD `longname` VARCHAR( 255 ) NOT NULL AFTER `password`;
UPDATE `gestion`.`editor` SET `longname` = 'Eric Barolet' WHERE `editor`.`k` =1;

/* add password index */
ALTER TABLE `editor` ADD INDEX ( `password` );

/* add lang */
ALTER TABLE `editor` ADD `lang` VARCHAR( 10 ) NOT NULL DEFAULT 'fr' AFTER `longname` ;

/* add storage */
ALTER TABLE `editor` ADD `storage` TEXT NOT NULL;

/* create table groupEditor */
CREATE TABLE `gestion`.`groupEditor` (
`k` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 30 ) NOT NULL ,
`longname` VARCHAR( 255 ) NOT NULL ,
`parentK` MEDIUMINT NOT NULL DEFAULT '0'
) ENGINE = MyISAM;

/* add active */
ALTER TABLE `groupEditor` ADD `active` TINYINT NOT NULL DEFAULT '1' AFTER `name`;
ALTER TABLE `groupEditor` CHANGE `active` `active` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '1';
