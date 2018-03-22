<?php
return <<<SQL
CREATE TABLE `AgreementType` (
  `AgreementTypeId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `TypeKey` varchar(45) DEFAULT NULL,
  `Description` varchar(45) DEFAULT NULL,
  `TypeOrder` tinyint(3) unsigned NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AgreementTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
;