CREATE TABLE `Announcement` (
  `AnnouncementId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Title` varchar(45) NOT NULL,
  `Description` text NOT NULL,
  `DateTimeBegin` datetime NOT NULL,
  `DateTimeEnd` datetime NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AnnouncementID`),
  KEY `idx_DateTimeBegin` (`DateTimeBegin`),
  KEY `idx_DateTimeEnd` (`DateTimeEnd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `IdentityAnnouncements` (
  `RecordId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(11) NOT NULL,
  `LastAcknowledged` datetime NOT NULL,
  `CreatedAt` datetime NOT NULL,
  `LastModified` datetime NOT NULL,
  PRIMARY KEY (`RecordId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', 'US3477', 'US3477.sql', NULL);