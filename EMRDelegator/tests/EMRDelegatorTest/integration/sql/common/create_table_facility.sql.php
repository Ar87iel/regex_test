<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `Facility` (
  `FacilityId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) DEFAULT NULL,
  `CompanyId` int(10) unsigned NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LastModified` datetime DEFAULT NULL,
  PRIMARY KEY (`FacilityId`,`CompanyId`),
  KEY `fk_Facility_Company1_idx` (`CompanyId`),
  CONSTRAINT `fk_Facility_Company1` FOREIGN KEY (`CompanyId`) REFERENCES `Company` (`CompanyId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
