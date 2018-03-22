ALTER TABLE `Company` DROP COLUMN `MigrationStatus`;

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US2823.sql';