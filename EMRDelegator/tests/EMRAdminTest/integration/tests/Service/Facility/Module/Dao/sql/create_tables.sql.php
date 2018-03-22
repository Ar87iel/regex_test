<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `Module` (
  `ModuleID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) DEFAULT NULL,
  `Description` tinytext,
  `Status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ModuleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
