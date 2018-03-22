
CREATE TABLE `Company` (
  `CompanyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) DEFAULT NULL,
  `OnlineStatus` varchar(40) DEFAULT 'None',
  `ClusterId` int(10) unsigned NOT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`CompanyId`),
  KEY `ndx_OnlineStatus` (`OnlineStatus`),
  KEY `ndx_ClusterId` (`ClusterId`),
  CONSTRAINT `fk_Company_Cluster` FOREIGN KEY (`ClusterId`) REFERENCES `Cluster` (`ClusterId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `DatabaseVersion` ( `Release` , `Feature` , `WorkItem` , `Script` )
VALUES ( '1.0.0', 'F99', 'US2771', 'US2771.sql' );
