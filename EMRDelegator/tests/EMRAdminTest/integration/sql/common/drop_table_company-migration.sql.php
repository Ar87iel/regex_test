<?php
return <<<SQL
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `CompanyMigration`;
SET foreign_key_checks = 1;
SQL
    ;