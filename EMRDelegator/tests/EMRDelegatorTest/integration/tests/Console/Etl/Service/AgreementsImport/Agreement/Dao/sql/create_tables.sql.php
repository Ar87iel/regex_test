<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `USR_Agreements` (
  `Agrmnts_AgreementID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Agrmnts_Type` tinyint(3) unsigned NOT NULL,
  `Agrmnts_Version` varchar(45) NOT NULL,
  `Agrmnts_Date` date NOT NULL,
  `Agrmnts_Preface` longtext,
  `Agrmnts_AgreementText` longtext NOT NULL,
  PRIMARY KEY (`Agrmnts_AgreementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `USR_AgreementType` (
  `AgreementTypeId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Type` varchar(45) DEFAULT NULL,
  `Description` varchar(45) DEFAULT NULL,
  `Order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`AgreementTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SQL;
