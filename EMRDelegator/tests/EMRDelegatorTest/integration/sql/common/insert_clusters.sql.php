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
true,
'None',
'blah blah',
'2013-03-26',
'2013-03-26'
);
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
(2,
'mycluster2',
502,
102,
true,
'None',
'blah blah',
'2013-03-26',
'2013-03-26'
);
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
(3,
'mycluster3',
503,
103,
true,
'None',
'blah blah',
'2013-03-26',
'2013-03-26'
);
SQL;
