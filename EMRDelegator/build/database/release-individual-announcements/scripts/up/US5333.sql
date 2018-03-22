CREATE TABLE `FacilityHasAnnouncement` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FacilityId` int(10) unsigned NOT NULL,
  `AnnouncementId` int(10) unsigned NOT NULL,
   PRIMARY KEY (`Id`) ,
   INDEX (AnnouncementId)
); 
CREATE TABLE `IdentityHasAnnouncement` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL,
  `AnnouncementId` int(10) unsigned NOT NULL,
   PRIMARY KEY (`Id`) ,
   INDEX (AnnouncementId)
); 

ALTER TABLE `Announcement` 
ADD COLUMN `Private` TINYINT(1) NOT NULL DEFAULT 0 AFTER `Description`;


-- Update kx modifier notice to end at end of Jan (targets prod specifically)
Update Announcement SET DateTimeEnd = '2014-01-30 00:00:00' where AnnouncementId = 2 AND Title = 'System Reminder' AND DateTimeBegin = '2013-12-30 00:00:00' ;