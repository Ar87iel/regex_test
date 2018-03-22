
ALTER TABLE `UserHasFacility` modify `IsDefault` bit NOT NULL DEFAULT false;

ALTER TABLE `PatientHasFacility` modify `IsDefault` bit NOT NULL DEFAULT false;

ALTER TABLE `Cluster` modify `AcceptingNewCompanies` bit NOT NULL DEFAULT false;

DELETE FROM `DatabaseVersion` WHERE Script = 'DE1714.sql';