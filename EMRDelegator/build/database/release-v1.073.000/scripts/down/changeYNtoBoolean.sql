ALTER TABLE `UserHasFacility` CHANGE COLUMN `IsDefault` `IsDefault` VARCHAR(1) NOT NULL DEFAULT 'N';
UPDATE `UserHasFacility` SET `IsDefault` = 'Y' WHERE `IsDefault` = '1';
UPDATE `UserHasFacility` SET `IsDefault` = 'N' WHERE `IsDefault` = '0';

ALTER TABLE `PatientHasFacility` CHANGE COLUMN `IsDefault` `IsDefault` VARCHAR(1) NOT NULL DEFAULT 'N';
UPDATE `PatientHasFacility` SET `IsDefault` = 'Y' WHERE `IsDefault` = '1';
UPDATE `PatientHasFacility` SET `IsDefault` = 'N' WHERE `IsDefault` = '0';

ALTER TABLE `Cluster` CHANGE COLUMN `AcceptingNewCompanies` `AcceptingNewCompanies` VARCHAR(1) NOT NULL DEFAULT 'Y';
UPDATE `Cluster` SET `AcceptingNewCompanies` = 'Y' WHERE `AcceptingNewCompanies` = '1';
UPDATE `Cluster` SET `AcceptingNewCompanies` = 'N' WHERE `AcceptingNewCompanies` = '0';

DELETE FROM `DatabaseVersion` WHERE `script` = 'changeYNtoBoolean.sql';