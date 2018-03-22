<?php
return <<<SQL
CREATE TABLE IF NOT EXISTS `IdentityAnnouncements` (
  `recordId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `identityId` int(11) NOT NULL,
  `lastAcknowledged` datetime NOT NULL,
  `createdAt` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  PRIMARY KEY (`recordId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
