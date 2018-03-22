<?php
return <<<SQL
INSERT INTO `Cluster`
(`ClusterId`,
`Name`,
`MaxFacilityCount`,
`CurrentFacilityCount`,
`AcceptingNewCompanies`,
`OnlineStatus`,
`Comment`,
`CreatedAt`,
`LastModified`)
VALUES
(1,
'mycluster1',
501,
101,
'Y',
'None',
'blah blah',
'2013-03-26',
'2013-03-26'
);
SQL;
