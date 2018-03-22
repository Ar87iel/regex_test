<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `Company` (
  `CompanyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) DEFAULT NULL,
  `OnlineStatus` varchar(40) DEFAULT 'None',
  `ClusterId` int(10) unsigned NOT NULL,
  `MigrationStatus` varchar(40) DEFAULT 'Ready',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LastModified` datetime DEFAULT NULL,
  PRIMARY KEY (`CompanyId`),
  KEY `ndx_OnlineStatus` (`OnlineStatus`),
  KEY `fk_Company_Cluster_idx` (`ClusterId`),
  CONSTRAINT `fk_Company_Cluster` FOREIGN KEY (`ClusterId`) REFERENCES `Cluster` (`ClusterId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
