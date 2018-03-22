<?php
return <<<SQL
CREATE TABLE `UserHasAgreement` (
  `RecordId`int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL,
  `AgreementId` smallint(5) unsigned NOT NULL,
  `RemoteAddress` varchar(15) DEFAULT NULL,
  `JobTitle` varchar(45) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
;