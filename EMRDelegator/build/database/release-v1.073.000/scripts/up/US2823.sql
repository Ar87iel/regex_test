ALTER TABLE `Company`
  ADD COLUMN `MigrationStatus` VARCHAR(40) DEFAULT 'Ready' AFTER `ClusterId` ;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', 'US2823', 'US2823.sql', NULL);