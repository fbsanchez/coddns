
set autocommit = 0;
DELIMITER $$

DROP PROCEDURE IF EXISTS `upgrade_coddns_db`;

CREATE PROCEDURE `upgrade_coddns_db` (target_dbschema int)
upgrade_coddns:BEGIN
    DECLARE `_Exception_Detected` BOOL DEFAULT 0;
    DECLARE dbschema text;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_Exception_Detected` = 1;

    START TRANSACTION;

    -- retrieve dbschema version
    select `value` INTO dbschema from settings where field = "dbschema";

    IF (dbschema+1 = target_dbschema) THEN
        -- All OK, apply update
        select "TARGET schema OK" as message;

    ELSEIF (dbschema >= target_dbschema) THEN
        -- already applied dbschema
        select concat("Already applied schema: ", target_dbschema, " current: ", dbschema) as message;
        LEAVE upgrade_coddns;
    ELSE 
        -- unexistent field
        select concat("Creating new dbschema version: ", (target_dbschema-1)) as message;
        set dbschema = target_dbschema - 1;
        insert into settings (field,value) values ("dbschema", concat(dbschema));
        COMMIT;
        -- ALL OK, apply update

    END IF;



    -- Update database schema
    
    IF (dbschema = 0) THEN
        -- UPGRADE 0 => 1
        select "Upgrading schema...";
        -- queries for update '0' => '1'


    END IF;




 

    IF `_Exception_Detected` THEN
        ROLLBACK;
        select "Failed to update schema, no changes made." as message;
        show warnings;
    ELSE
        update settings set `value`= target_dbschema where field = "dbschema";
        COMMIT;
        select concat ("Successfully updated to ", target_dbschema) as message;
    END IF;
    
END$$

DELIMITER ;
set autocommit = 1;

