CREATE TABLE if not exists `DatabaseVersion` (
  `VersionID` int(11) NOT NULL AUTO_INCREMENT,
  `Release` varchar(10),
  `Feature` varchar(10),
  `WorkItem` varchar(10),
  `Script` varchar(50) NOT NULL,
  `Timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VersionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into version ( `release` , script , created , modified , version )
select 'EMRDelegator-release-v1.073.000' , Script , `Timestamp` , `Timestamp` , 1
from DatabaseVersion;
