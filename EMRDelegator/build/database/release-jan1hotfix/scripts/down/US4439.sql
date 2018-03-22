DELETE FROM `Agreement` WHERE `AgreementTypeId` = '3' AND AgreementDate =  '2014-01-01';
DELETE FROM `Agreement` WHERE `AgreementTypeId` = '2' AND AgreementDate =  '2014-01-01';
DELETE FROM `Agreement` WHERE `AgreementTypeId` = '4' AND AgreementDate =  '2014-01-01';

DELETE FROM `DatabaseVersion` WHERE `Script` = 'US4439.sql';