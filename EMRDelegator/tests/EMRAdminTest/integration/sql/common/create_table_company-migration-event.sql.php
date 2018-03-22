<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `CompanyMigrationEvent` (
  `RecordId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `MigrationId` int(11) unsigned NOT NULL,
  `Event` varchar(64) NOT NULL,
  `CreatedDateTime` datetime NOT NULL,
  `Message` TEXT DEFAULT NULL,
  PRIMARY KEY (`RecordId`),
  KEY `ndx_MigrationId` (`MigrationId`),
  CONSTRAINT `fk_CompanyMigrationEvent_MigrationId` FOREIGN KEY (`MigrationId`) REFERENCES `CompanyMigration` (`MigrationId`) ON DELETE NO ACTION ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;


