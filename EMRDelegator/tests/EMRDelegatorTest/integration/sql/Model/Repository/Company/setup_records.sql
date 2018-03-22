insert into Cluster (ClusterId, `Name`) values (10, 'c1');

insert into Company (CompanyId, `Name`, ClusterId) values (10, 'a', 10);
insert into Company (CompanyId, `Name`, ClusterId) values (20, 'b', 10);
insert into Company (CompanyId, `Name`, ClusterId) values (30, 'c', 10);

insert into Facility (FacilityId, CompanyId, `Name`) values (10, 10, 'fd');
insert into Facility (FacilityId, CompanyId, `Name`) values (20, 20, 'ga');
insert into Facility (FacilityId, CompanyId, `Name`) values (30, 20, 'gba');
insert into Facility (FacilityId, CompanyId, `Name`) values (40, 30, 'ha');
insert into Facility (FacilityId, CompanyId, `Name`) values (50, 30, 'hb');
insert into Facility (FacilityId, CompanyId, `Name`) values (60, 30, 'hc');