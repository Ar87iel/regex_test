<?php
return <<<SQL

INSERT INTO `SessionRegistry` (`SessionRegistryId`, `IdentityId`, `SsoToken`, `SessionId`, `Created`, `LastModified`)
VALUES
(1, 27, 'abcdefg123', 'poiuytrewq', NOW(), NOW()),
(2, 28, 'hijklmn456', 'lkjhgfdsa', NOW(), NOW());


SQL;


