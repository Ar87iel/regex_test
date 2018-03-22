SET foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS `UserHasFacility` (
  `RecordId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL DEFAULT '0',
  `IsDefault` char(1) DEFAULT 'N',
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


CREATE TABLE IF NOT EXISTS `PatientHasFacility` (
  `RecordId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL DEFAULT '0',
  `IsDefault` char(1) DEFAULT 'N',
  `FacilityId` int(10) unsigned NOT NULL,
  `PatientId` int(10) unsigned NOT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordId`),
  UNIQUE KEY `ndx_IdentityId_PatientId_FacilityId` (`IdentityId`,`FacilityId`,`PatientId`),
  KEY `ndx_IdentityId_Default` (`IdentityId`,`IsDefault`),
  KEY `ndx_FacilityId` (`FacilityId`),
  CONSTRAINT `fk_PatientHasFacility_Facility1` FOREIGN KEY (`FacilityId`) REFERENCES `Facility` (`FacilityId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

SET foreign_key_checks = 1;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', 'US2819', 'US2819.sql', NULL);