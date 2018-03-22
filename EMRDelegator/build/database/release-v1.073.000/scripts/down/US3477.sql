DROP TABLE IF EXISTS `LoginAnnouncement`;

DROP TABLE IF EXISTS `IdentityAnnouncements`;

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US3477.sql';