<?php
return <<<SQL
insert into USR_UserHasAgreement VALUES
  (1, 1, '2013-05-01 00:00:00', 'ip', '');

insert into UserHasAgreement VALUES
  (1, 1, 1, 'ip','','2013-05-01 00:00:00',now());
SQL;
