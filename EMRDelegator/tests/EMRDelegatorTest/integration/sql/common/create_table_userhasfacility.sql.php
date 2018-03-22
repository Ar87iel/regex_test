<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `UserHasFacility` (
  `RecordId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL DEFAULT '0',
  `IsDefault` bit DEFAULT false,
  `FacilityId` int(10) unsigned NOT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordId`),
  UNIQUE KEY `ndx_IdentityId_FacilityId` (`IdentityId`, `FacilityId`),
  KEY `ndx_IdentityId_Default` (`IdentityId`,`IsDefault`),
  KEY `ndx_IdentityId` (`IdentityId`),
  KEY `ndx_FacilityId_idx` (`FacilityId`),
  CONSTRAINT `fk_UserHasFacility_Facility1` FOREIGN KEY (`FacilityId`) REFERENCES `Facility` (`FacilityId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
