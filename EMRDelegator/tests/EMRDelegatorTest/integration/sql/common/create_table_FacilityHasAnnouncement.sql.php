<?php
return <<<SQL

CREATE TABLE `FacilityHasAnnouncement` (
`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`FacilityId` int(10) unsigned NOT NULL,
`AnnouncementId` int(10) unsigned NOT NULL,
PRIMARY KEY (`Id`) ,
INDEX (AnnouncementId)
);
SQL;


