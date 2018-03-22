ALTER TABLE `UserHasAgreement` ADD COLUMN `JobTitle` varchar(45) DEFAULT NULL AFTER RemoteAddress;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', '', 'US4490', 'US4490.sql', NULL);