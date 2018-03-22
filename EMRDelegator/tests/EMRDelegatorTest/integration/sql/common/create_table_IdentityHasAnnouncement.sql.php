<?php
return <<<SQL
CREATE TABLE `IdentityHasAnnouncement` (
`Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`IdentityId` int(10) unsigned NOT NULL,
`AnnouncementId` int(10) unsigned NOT NULL,
PRIMARY KEY (`Id`) ,
INDEX (AnnouncementId)
);
SQL;

 
