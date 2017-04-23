-- MySQL upgrade procedure

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
        select concat("Schema already applied: ", target_dbschema, " current: ", dbschema) as message;
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

        -- table clusters
        CREATE TABLE IF NOT EXISTS clusters (
            id serial,
            tag varchar(200) NOT NULL default "default" UNIQUE,
            gid bigint unsigned NOT NULL default 1,
            status int DEFAULT 0,
            CONSTRAINT fkey_cluster_group  FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
        ) engine=InnoDB;


        -- updates over servers table
        ALTER TABLE servers 
            ADD COLUMN cluster_id bigint unsigned DEFAULT NULL,
            ADD COLUMN server_load int unsigned DEFAULT 0,
            ADD COLUMN mastery int unsigned DEFAULT 100,
            ADD CONSTRAINT fkey_servers_cluster FOREIGN KEY (cluster_id) REFERENCES clusters(id) ON DELETE SET NULL ON UPDATE CASCADE;


        -- new table zone_server
        CREATE TABLE IF NOT EXISTS zone_server (
            id serial,
            id_zone bigint unsigned NOT NULL,
            id_server bigint unsigned NOT NULL,
            id_master bigint unsigned DEFAULT NULL,
            rep_status int unsigned DEFAULT NULL,
            ref_type int unsigned DEFAULT NULL,
            CONSTRAINT pkey_zs PRIMARY KEY (id),
            CONSTRAINT fkey_zs_zone FOREIGN KEY (id_zone) REFERENCES zones(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fkey_zs_server FOREIGN KEY (id_server) REFERENCES servers(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fkey_zs_master FOREIGN KEY (id_master) REFERENCES servers(id)
        ) engine=InnoDB;


        -- recover zone - server relations
        INSERT INTO zone_server (id_zone,id_server,id_master) (select id as id_zone, server_id as id_server, master_id from zones);

        -- updates over zones table
        ALTER TABLE zones 
            DROP FOREIGN KEY fkey_zones_server,
            DROP FOREIGN KEY fkey_zones_master,
            DROP COLUMN server_id,
            DROP COLUMN master_id,
            ADD CONSTRAINT fkey_zones_gid FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT const_zone_unique_domain UNIQUE (domain);
    END IF;



    -- IF (dbschema = 1) THEN
        -- UPGRADE 1 => 2
    -- END IF;

 

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


call upgrade_coddns_db(1);
