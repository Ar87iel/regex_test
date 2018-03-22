<?php
return <<<SQL
CREATE TABLE `SessionRegistry` (
  `SessionRegistryId` int(11) NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL,
  `SsoToken` char(32) NOT NULL,
  `Created` datetime DEFAULT NULL,
  `LastModified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SessionRegistryId`),
  UNIQUE KEY `udx_IdentityId` (`IdentityId`),
  UNIQUE KEY `udx_SsoToken` (`SsoToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ;