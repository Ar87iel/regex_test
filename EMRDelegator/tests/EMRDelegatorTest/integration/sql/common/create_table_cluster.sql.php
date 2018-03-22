<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `Cluster` (
`ClusterId` int(10) unsigned NOT NULL AUTO_INCREMENT,
`Name` varchar(128) DEFAULT NULL,
`MaxFacilityCount` int(10) unsigned NOT NULL DEFAULT '0',
`CurrentFacilityCount` int(10) unsigned NOT NULL DEFAULT '0',
`AcceptingNewCompanies` BIT NOT NULL DEFAULT false,
`OnlineStatus` varchar(40) DEFAULT 'None',
`Comment` text,
`CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`LastModified` datetime DEFAULT NULL,
PRIMARY KEY (`ClusterId`),
KEY `ndx_AcceptingNewCompanies` (`AcceptingNewCompanies`),
KEY `ndx_OnlineStatus` (`OnlineStatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
