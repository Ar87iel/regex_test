<?php
return <<<SQL
insert into USR_AgreementType VALUES
  (1, 'Type', 'Description', 2);

insert into AgreementType VALUES
  (3, 'Type', 'Description', 3, now(), now());

SQL;
