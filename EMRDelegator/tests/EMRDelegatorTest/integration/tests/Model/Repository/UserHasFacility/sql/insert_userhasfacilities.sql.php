<?php
return <<<SQL
INSERT INTO `UserHasFacility` (`RecordId` , `FacilityId` , `IdentityId` , `IsDefault` , `CreatedAt` , `LastModified`)
VALUES
  ( 1 , 1 , 1 , 0 , '2013-03-26' , '2013-03-26' )
  , ( 2 , 2 , 1 , 0 , '2013-03-26' , '2013-03-26' )
  , ( 3 , 3 , 1 , 0 , '2013-03-26' , '2013-03-26' )
  , ( 4 , 4 , 1 , 1 , '2013-03-26' , '2013-03-26' )
  , ( 5 , 5 , 1 , 0 , '2013-03-26' , '2013-03-26' )
;

SQL;



