
CREATE  TABLE IF NOT EXISTS `version` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `release` VARCHAR(45) NOT NULL ,
    `script` VARCHAR(45) NOT NULL ,
    `created` DATETIME NOT NULL ,
    `modified` DATETIME NOT NULL ,
    `version` INT UNSIGNED NOT NULL DEFAULT 1 ,
    PRIMARY KEY (`id`) ,
    UNIQUE INDEX `release_script` (`release` ASC, `script` ASC) )
    ENGINE = InnoDB;

update version set `release` = substr( `release` , 10 )
where `release` like 'EMRDelegator-%' ;
