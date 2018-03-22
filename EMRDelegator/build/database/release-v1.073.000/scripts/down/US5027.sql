
DELETE FROM `Announcement` WHERE `Title` = 'Quarterly Survey' AND `DateTimeBegin` = '2013-12-12 08:00:00' AND `DateTimeEnd` = '2013-12-13 08:00:00';

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US5027.sql';