CREATE TABLE IF NOT EXISTS `AgreementType` (
  `AgreementTypeId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `TypeKey` varchar(45) DEFAULT NULL,
  `Description` varchar(45) DEFAULT NULL,
  `TypeOrder` tinyint(3) unsigned NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AgreementTypeId`),
  UNIQUE KEY `Order_UNIQUE` (`TypeOrder`),
  UNIQUE KEY `Type_UNIQUE` (`TypeKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Agreement` (
  `AgreementId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `AgreementTypeId` tinyint(3) unsigned NOT NULL,
  `Version` varchar(45) NOT NULL,
  `AgreementDate` date NOT NULL,
  `Preface` longtext,
  `Text` longtext NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AgreementId`),
  UNIQUE KEY `idx_Type_Version` (`AgreementTypeId`,`Version`),
  CONSTRAINT `Agreements_ibfk_1` FOREIGN KEY (`AgreementTypeId`) REFERENCES `AgreementType` (`AgreementTypeId`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `UserHasAgreement` (
  `RecordId`int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL,
  `AgreementId` smallint(5) unsigned NOT NULL,
  `RemoteAddress` varchar(15) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordId`),
  UNIQUE KEY `udx_Identity_Agreement` (`IdentityId`,`AgreementId`),
  KEY `idx_AgreementId` (`AgreementId`),
  CONSTRAINT `fk_AgreementId` FOREIGN KEY (`AgreementId`) REFERENCES `Agreement` (`AgreementId`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', 'US3475', 'US3475.sql', NULL);