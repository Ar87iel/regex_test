<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `Preference` (
  `PreferenceID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) DEFAULT NULL,
  `Description` tinytext,
  `GroupID` int(10) unsigned DEFAULT NULL,
  `PreferenceOrder` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`PreferenceID`)
) ENGINE=InnoDB;

CREATE TABLE `PreferenceGroup` (
  `GroupID` int(10) unsigned NOT NULL,
  `GroupOrder` tinyint(3) unsigned DEFAULT NULL,
  `GroupName` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `GroupDescription` tinytext,
  PRIMARY KEY (`GroupID`)
) ENGINE=InnoDB;
SQL;
