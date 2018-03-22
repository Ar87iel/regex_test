<?php
return <<<SQL

INSERT INTO `SessionRegistry` (`SessionRegistryId`, `IdentityId`, `SsoToken`, `Created`, `LastModified`)
VALUES
(1, 27, 'abcdefg123', NOW(), NOW()),
(2, 28, 'hijklmn456', NOW(), NOW());


SQL;


