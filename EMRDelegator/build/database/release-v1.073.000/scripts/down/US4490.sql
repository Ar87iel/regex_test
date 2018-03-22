ALTER TABLE `UserHasAgreement` DROP COLUMN `JobTitle`;

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US4490.sql';