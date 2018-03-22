<?php
return <<<SQL
INSERT INTO `CompanyMigration` (`MigrationId`, `IdentityId`, `CompanyId`, `DestinationClusterId`, `CreatedDateTime`, `CompletedDateTime`, `CompletedState`)
VALUES
	(3, 55, 456, 2, '2013-05-31 22:14:44', '2013-05-31 22:14:51', 'success'),
	(4, 55, 456, 2, '2013-06-01 23:14:44', '2013-06-01 23:14:51', 'failure');



SQL;


