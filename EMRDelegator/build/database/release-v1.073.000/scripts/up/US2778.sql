CREATE TABLE IF NOT EXISTS `Facility` (
  `FacilityId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) DEFAULT NULL,
  `CompanyId` int(10) unsigned NOT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`FacilityId`,`CompanyId`),
  KEY `ndx_CompanyId` (`CompanyId`),
  CONSTRAINT `fk_Facility_CompanyId` FOREIGN KEY (`CompanyId`) REFERENCES `Company` (`CompanyId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `DatabaseVersion` VALUES (NULL, '1.0.0', 'F99', 'US2778', 'US2778.sql', NULL);