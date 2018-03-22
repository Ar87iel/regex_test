<?php
return <<<SQL

insert into USR_AgreementType VALUES
  (1, 'Type', 'Description', 2);

insert into USR_Agreements VALUES
  (1, 1, '1.0', '2013-05-01', 'preface', 'text');


insert into AgreementType VALUES
  (2, 'Type', 'Description', 3, now(), now());

insert into Agreement VALUES
  (3, 2, '1.0', '2013-05-01', 'preface', 'text', now(), now());
SQL;
