<?php
return <<<SQL
INSERT INTO `Company`
(`CompanyId`,
`Name`,
`OnlineStatus`,
`ClusterId`,
`CreatedAt`,
`LastModified`)
VALUES
(1,
'mycompany1',
'None',
1,
'2013-03-26',
'2013-03-26'
);
INSERT INTO `Company`
(`CompanyId`,
`Name`,
`OnlineStatus`,
`ClusterId`,
`CreatedAt`,
`LastModified`)
VALUES
(2,
'mycompany2',
'None',
1,
'2013-03-26',
'2013-03-26'
);
SQL;
