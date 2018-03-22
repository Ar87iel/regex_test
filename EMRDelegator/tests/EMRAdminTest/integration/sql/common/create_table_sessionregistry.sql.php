<?php
return <<<SQL
CREATE TABLE `SessionRegistry` (
  `SessionRegistryId` int(11) NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL,
  `SsoToken` char(32) NOT NULL,
  `SessionId` char(32) NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SessionRegistryId`),
  UNIQUE KEY `udx_IdentityId` (`IdentityId`),
  UNIQUE KEY `udx_SsoToken` (`SsoToken`),
  UNIQUE KEY `udx_SessionId` (`SessionId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SQL
    ;