<?php
return <<<SQL

INSERT INTO AgreementType VALUES (1, 'TYPE1', 'Agreement type 1', 1, NOW(), NOW());
INSERT INTO AgreementType VALUES (2, 'TYPE2', 'Agreement type 2', 2, NOW(), NOW());
INSERT INTO AgreementType VALUES (3, 'TYPE3', 'Agreement type 3', 3, NOW(), NOW());

INSERT INTO Agreement VALUES (1, 1, 'version 1', '2013-01-01', 'Agreement 1 Preface', 'Text 1', NOW(), NOW());
INSERT INTO Agreement VALUES (2, 2, 'version 2', '2013-01-01', 'Agreement 2 Preface', 'Text 2', NOW(), NOW());
INSERT INTO Agreement VALUES (3, 3, 'version 3', '2013-01-01', 'Agreement 3 Preface', 'Text 3', NOW(), NOW());

INSERT INTO UserHasAgreement VALUES (1, 1, 1, NULL, NULL, NOW(), NOW());
INSERT INTO UserHasAgreement VALUES (2, 2, 2, NULL, NULL, NOW(), NOW());

SQL
    ;