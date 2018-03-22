<?php
return <<<SQL
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `PatientHasFacility`;
SET foreign_key_checks = 1;
SQL;
