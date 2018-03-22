/** Facility
  `FacilityId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) DEFAULT NULL,
  `CompanyId` int(10) unsigned NOT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

 */

insert into Facility (FacilityId, CompanyId) values (111,1);
insert into Facility (FacilityId, CompanyId) values (112,2);
insert into Facility (FacilityId, CompanyId) values (113,3);
insert into Facility (FacilityId, CompanyId) values (114,4);

/* UserHasFacility
  `RecordId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentityId` int(10) unsigned NOT NULL DEFAULT '0',
  `IsDefault` char(1) DEFAULT 'N',
  `FacilityId` int(10) unsigned NOT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
*/

/* User with multiple facilities. isDefault is not set */
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (1,'N',111);
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (1,'N',112);
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (1,'N',113);
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (1,'N',114);
/* User with multiple facilities. isDefault set*/
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (2,'N',111);
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (2,'N',112);
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (2,'Y',113);
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (2,'N',114);
/* User with one facility*/
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (3,'N',111);
/* User with no facility*/
insert into UserHasFacility (IdentityId, IsDefault, FacilityId) values (3,'N','');




