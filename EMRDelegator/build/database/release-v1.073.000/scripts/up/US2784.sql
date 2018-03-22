CREATE TABLE IF NOT EXISTS `Cluster` (
  `ClusterId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) DEFAULT NULL,
  `MaxFacilityCount` int(10) unsigned NOT NULL DEFAULT '0',
  `CurrentFacilityCount` int(10) unsigned NOT NULL DEFAULT '0',
  `AcceptingNewCompanies` char(1) DEFAULT 'Y',
  `OnlineStatus` varchar(10) DEFAULT 'None',
  `Comment` text,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ClusterId`),
  KEY `ndx_AcceptingNewCompanies` (`AcceptingNewCompanies`),
  KEY `ndx_OnlineStatus` (`OnlineStatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', 'US2784', 'US2784.sql', NULL);