<?php
return <<<SQL
CREATE TABLE  `CompanyMigration` (
  `MigrationId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(11) NOT NULL,
  `CompanyId` int(11) NOT NULL,
  `DestinationClusterId` int(11) NOT NULL,
  `CreatedDateTime` datetime NOT NULL,
  `CompletedDateTime` datetime,
  `CompletedState` varchar(8),
  PRIMARY KEY (`MigrationId`),
  KEY `ndx_MigrationId` (`MigrationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;


