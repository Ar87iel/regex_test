<?php

$tenDaysAgo = gmdate('Y-m-d H:i:s', strtotime('-10 days'));
$fiveDaysAgo = gmdate('Y-m-d H:i:s', strtotime('-5 days'));
$today = gmdate('Y-m-d H:i:s', strtotime('today'));
$fiveDaysFromNow = gmdate('Y-m-d H:i:s', strtotime('+5 days'));
$tenDaysFromNow = gmdate('Y-m-d H:i:s', strtotime('+10 days'));


return <<<SQL
INSERT INTO `Announcement`
(`AnnouncementId`,`Title`,`Description`,`DateTimeBegin`,`DateTimeEnd`,`Created`,`LastModified`,`Private`)
VALUES
(1,'Announcement 1','Announcement 1 Description', '{$tenDaysAgo}', '{$fiveDaysAgo}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,0),
(2,'Announcement 2','Announcement 2 Description', '{$fiveDaysAgo}', '{$today}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,0),
(3,'Announcement 3','Announcement 3 Description', '{$today}', '{$fiveDaysFromNow}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,0),
(4,'Announcement 4','Announcement 4 Description', '{$fiveDaysFromNow}', '{$tenDaysFromNow}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,0);

SQL;
