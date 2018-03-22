<?php
return <<<SQL
CREATE TABLE `Agreement` (
  `AgreementId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `AgreementTypeId` tinyint(3) unsigned NOT NULL,
  `Version` varchar(45) NOT NULL,
  `AgreementDate` date NOT NULL,
  `Preface` longtext,
  `Text` longtext NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AgreementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
;