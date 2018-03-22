<?php
return <<<SQL


INSERT INTO Cluster (ClusterId , Name,MaxFacilityCount, CurrentFacilityCount, AcceptingNewCompanies, OnlineStatus, Comment, CreatedAt, LastModified)
VALUES (1, 'mycluster1', 500 , 200 , 'Y' , 'None' , 'this is a comment' , '2013-03-26' , '2013-03-26');

INSERT INTO Company (CompanyId , Name, OnlineStatus ,ClusterId , CreatedAt , LastModified)
VALUES (1,'mycompany1' , 'None' , 1 , '2013-03-26' , '2013-03-26' );
INSERT INTO Company (CompanyId , Name, OnlineStatus ,ClusterId , CreatedAt , LastModified)
VALUES (2,'mycompany2' , 'None' , 1 , '2013-03-26' , '2013-03-26' );
INSERT INTO Company (CompanyId , Name, OnlineStatus ,ClusterId , CreatedAt , LastModified)
VALUES (3,'mycompany3' , 'None' , 1 , '2013-03-26' , '2013-03-26' );

insert into Facility (FacilityId , Name , CompanyId , CreatedAt , LastModified) values (1 , 'Clinic 1' , 1 , '2013-03-26' , '2013-03-26');
insert into Facility (FacilityId , Name , CompanyId , CreatedAt , LastModified) values (2 , 'Clinic 1' , 2 , '2013-03-26' , '2013-03-26');
insert into Facility (FacilityId , Name , CompanyId , CreatedAt , LastModified) values (3 , 'Clinic 1' , 3 , '2013-03-26' , '2013-03-26');

/* User with multiple facilities. isDefault is not set */
insert into UserHasFacility (RecordId , IdentityId ,  IsDefault ,  FacilityId ,  CreatedAt ,  LastModified) values (1 , 10 , 0 , 1, '2013-03-26' , '2013-03-26' );
-- insert into UserHasFacility (RecordId , IdentityId ,  IsDefault ,  FacilityId ,  CreatedAt ,  LastModified) values (2 , 10 , 0 , 2, '2013-03-26' , '2013-03-26' );
insert into UserHasFacility (RecordId , IdentityId ,  IsDefault ,  FacilityId ,  CreatedAt ,  LastModified) values (3 , 10 , 0 , 3, '2013-03-26' , '2013-03-26' );

/* User with one facility No default set */
insert into UserHasFacility (RecordId , IdentityId ,  IsDefault ,  FacilityId ,  CreatedAt ,  LastModified) values (4 , 11 , 0 , 1, '2013-03-26' , '2013-03-26' );
insert into UserHasFacility (RecordId , IdentityId ,  IsDefault ,  FacilityId ,  CreatedAt ,  LastModified) values (5 , 11 , 1 , 2, '2013-03-26' , '2013-03-26' );




SQL;



