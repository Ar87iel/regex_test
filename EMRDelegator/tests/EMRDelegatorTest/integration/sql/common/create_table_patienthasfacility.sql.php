<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `PatientHasFacility` (
  `RecordId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL DEFAULT '0',
  `IsDefault` bit DEFAULT false,
  `FacilityId` int(10) unsigned NOT NULL,
  `PatientId` int(10) unsigned NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LastModified` datetime DEFAULT NULL,
  PRIMARY KEY (`RecordId`),
  UNIQUE KEY `ndx_IdentityId_PatientId_FacilityId` (`IdentityId`),
  KEY `ndx_IdentityId_PatientId_Default` (`IdentityId`,`IsDefault`),
  KEY `ndx_IdentityId_PatientId_FacilityId_Default` (`IdentityId`,`IsDefault`),
  KEY `fk_PatientHasFacility_Facility1_idx` (`FacilityId`),
  CONSTRAINT `fk_PatientHasFacility_Facility1` FOREIGN KEY (`FacilityId`) REFERENCES `Facility` (`FacilityId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
