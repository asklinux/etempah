ALTER TABLE  `transports` ADD COLUMN `status` TINYINT(1) NULL DEFAULT 0;
ALTER TABLE  `transports` ADD COLUMN `status_description` VARCHAR(128) NULL DEFAULT '' AFTER `status`;

ALTER TABLE  `equipments` ADD COLUMN `status` TINYINT(1) NULL DEFAULT 0;
ALTER TABLE  `equipments` ADD COLUMN `status_description` VARCHAR(128) NULL DEFAULT '' AFTER `status`;