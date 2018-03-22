DROP TABLE IF EXISTS `Cluster`;

DELETE `DatabaseVersion` FROM `DatabaseVersion` WHERE `Script` = 'US2784.sql';