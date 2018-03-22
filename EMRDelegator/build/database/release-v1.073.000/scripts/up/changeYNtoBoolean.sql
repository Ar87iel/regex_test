UPDATE `UserHasFacility` SET `IsDefault` = 1 WHERE `IsDefault` = 'Y';
UPDATE `UserHasFacility` SET `IsDefault` = 0 WHERE `IsDefault` = 'N';
ALTER TABLE `UserHasFacility` CHANGE COLUMN `IsDefault` `IsDefault` bit NOT NULL DEFAULT false;

UPDATE `PatientHasFacility` SET `IsDefault` = 1 WHERE `IsDefault` = 'Y';
UPDATE `PatientHasFacility` SET `IsDefault` = 0 WHERE `IsDefault` = 'N';
ALTER TABLE `PatientHasFacility` CHANGE COLUMN `IsDefault` `IsDefault` bit NOT NULL DEFAULT false;

UPDATE `Cluster` SET `AcceptingNewCompanies` = 1 WHERE `AcceptingNewCompanies` = 'Y';
UPDATE `Cluster` SET `AcceptingNewCompanies` = 0 WHERE `AcceptingNewCompanies` = 'N';
ALTER TABLE `Cluster` CHANGE COLUMN `AcceptingNewCompanies` `AcceptingNewCompanies` bit NOT NULL DEFAULT false;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', NULL, 'changeYNtoBoolean.sql', NULL);