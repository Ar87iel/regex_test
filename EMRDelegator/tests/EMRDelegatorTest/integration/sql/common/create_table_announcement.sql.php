<?php
return <<<SQL
CREATE TABLE `Announcement` (
`AnnouncementId` int(10) unsigned NOT NULL AUTO_INCREMENT,
`Title` varchar(45) NOT NULL,
`Description` text NOT NULL,
`DateTimeBegin` datetime NOT NULL,
`DateTimeEnd` datetime NOT NULL,
`Created` datetime DEFAULT NULL,
`LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Private` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`AnnouncementID`),
KEY `idx_DateTimeBegin` (`DateTimeBegin`),
KEY `idx_DateTimeEnd` (`DateTimeEnd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
;