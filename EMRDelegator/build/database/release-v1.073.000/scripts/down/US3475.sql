DROP TABLE IF EXISTS `UserHasAgreement`;
DROP TABLE IF EXISTS `Agreement`;
DROP TABLE IF EXISTS `AgreementType`;

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US3475.sql';