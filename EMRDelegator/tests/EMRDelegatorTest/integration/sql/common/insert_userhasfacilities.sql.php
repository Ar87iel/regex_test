<?php
return <<<SQL
INSERT INTO `UserHasFacility`
(`RecordId`,
`FacilityId`,
`IdentityId`,
`IsDefault`,
`CreatedAt`,
`LastModified`)
VALUES
(1,
1,
11,
false,
'2013-03-26',
'2013-03-26'
);
INSERT INTO `UserHasFacility`
(`RecordId`,
`FacilityId`,
`IdentityId`,
`IsDefault`,
`CreatedAt`,
`LastModified`)
VALUES
(2,
1,
12,
false,
'2013-03-26',
'2013-03-26'
);
INSERT INTO `UserHasFacility`
(`RecordId`,
`FacilityId`,
`IdentityId`,
`IsDefault`,
`CreatedAt`,
`LastModified`)
VALUES
(3,
3,
33,
false,
'2013-03-26',
'2013-03-26'
);
SQL;
