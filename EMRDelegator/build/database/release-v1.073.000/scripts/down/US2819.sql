DROP TABLE IF EXISTS `UserHasFacility;
DROP TABLE IF EXISTS `PatientHasFacility;

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US2819.sql';