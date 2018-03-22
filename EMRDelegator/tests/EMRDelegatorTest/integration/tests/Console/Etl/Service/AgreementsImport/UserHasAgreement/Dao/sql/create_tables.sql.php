<?php
return <<<SQL
-- Create syntax for TABLE 'USR_UserHasAgreement'
CREATE TABLE IF NOT EXISTS `USR_UserHasAgreement` (
  `Usrhagrd_UserID` int(10) unsigned NOT NULL,
  `Usrhagrd_AgreementID` smallint(5) unsigned NOT NULL,
  `Usrhagrd_DateTime` datetime NOT NULL,
  `Usrhagrd_RemoteAddress` varchar(15) DEFAULT NULL,
  `Usrhagrd_JobTitle` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Usrhagrd_UserID`,`Usrhagrd_AgreementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SQL;
