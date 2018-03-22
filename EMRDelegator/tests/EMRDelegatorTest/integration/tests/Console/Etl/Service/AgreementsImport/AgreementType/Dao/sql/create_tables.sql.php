<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `USR_AgreementType` (
  `AgreementTypeId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Type` varchar(45) DEFAULT NULL,
  `Description` varchar(45) DEFAULT NULL,
  `Order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`AgreementTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
