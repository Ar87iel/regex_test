<?php
return <<< SQL
INSERT INTO `Preference` (`PreferenceID`, `Name`, `Description`, `GroupID`, `PreferenceOrder`)
VALUES
	(1, 'Foo', 'Foo Desc', 1, 2),
    (2, 'Bar', 'Bar Cust', 1, 1),
	(3, 'Biz', 'Biz Desc', 2, 3);

INSERT INTO `PreferenceGroup` (`GroupID`, `GroupOrder`, `GroupName`, `GroupDescription`)
VALUES
	(1, 2, 'One', 'Group One'),
	(2, 1, 'Two', 'Group Two');

SQL;
