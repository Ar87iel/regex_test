
ALTER TABLE `UserHasFacility` modify `IsDefault` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `PatientHasFacility` modify `IsDefault` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `Cluster` modify `AcceptingNewCompanies` tinyint(1) NOT NULL DEFAULT 0;

INSERT INTO `DatabaseVersion` VALUES (NULL, '2.0.0', NULL, 'DE1714', 'DE1714.sql', NULL);