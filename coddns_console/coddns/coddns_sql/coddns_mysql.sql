-- 
-- SQL script for CODDNS system
-- 
-- create database db_ddnsp;
-- GRANT ALL PRIVILEGES ON db_ddnsp.* TO ddnsp@'127.0.0.1' identified by 'p4ssw0rd';

-- Table roles
CREATE TABLE IF NOT EXISTS roles (
    id serial,
    tag varchar(200) NOT NULL,
    auth_level int NOT NULL DEFAULT 1,
    description text,
    CONSTRAINT pkey_roles PRIMARY KEY (id)
) engine=InnoDB;


-- Table users
CREATE TABLE IF NOT EXISTS users (
    id serial,
    mail varchar(250) NOT NULL,
    pass text NOT NULL,
    first_login timestamp DEFAULT CURRENT_TIMESTAMP,
    last_login timestamp,
    ip_last_login int,
    ip_first_login int,
    hash varchar(255),
    max_time_valid_hash timestamp,
    rol bigint unsigned,
	CONSTRAINT pkey_users PRIMARY KEY (id),
    CONSTRAINT fkey_users_rol FOREIGN KEY (rol) REFERENCES roles(id) ON DELETE SET NULL ON UPDATE CASCADE,
	CONSTRAINT const_usuarios_unique_mail UNIQUE (mail),
	CONSTRAINT users_hash_key UNIQUE (hash)
) engine=InnoDB;


-- Table groups
CREATE TABLE IF NOT EXISTS groups (
    id serial,
    tag varchar(200) NOT NULL,
    description text,
    parent bigint unsigned,
    CONSTRAINT pkey_groups PRIMARY KEY (id),
    CONSTRAINT fkey_group_parent FOREIGN KEY (parent) REFERENCES groups(id) ON DELETE CASCADE
) engine=InnoDB;


-- Table object_types
CREATE TABLE IF NOT EXISTS object_types (
    id serial,
    tag varchar(200) NOT NULL,
    description text,
    CONSTRAINT pkey_object_types PRIMARY KEY (id)
) engine=InnoDB;


-- Table tusers_groups
CREATE TABLE IF NOT EXISTS tusers_groups (
    id serial,
    gid bigint unsigned NOT NULL,
    oid bigint unsigned NOT NULL,
    view  int(1) NOT NULL DEFAULT 0,
    edit  int(1) NOT NULL DEFAULT 0,
    admin int(1) NOT NULL DEFAULT 0,
    obj_type int DEFAULT null,
    CONSTRAINT pkey_user_group PRIMARY KEY (id),
    CONSTRAINT fkey_user_group_users FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_user_group_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
) engine=InnoDB;


-- Table Record types
CREATE TABLE IF NOT EXISTS record_types (
    id serial,
    tag varchar(200) NOT NULL,
    description text,
	auth_level int NOT NULL DEFAULT 100,
    CONSTRAINT pkey_record_types PRIMARY KEY (id)
) engine=InnoDB;


-- Table Servers
CREATE TABLE IF NOT EXISTS servers (
    id serial,
    tag varchar(200) NOT NULL default "default" UNIQUE,
    ip bigint,
    gid bigint unsigned NOT NULL default 1,
    srv_user varchar(200),
    srv_password text,
    config text,
    config_md5 varchar(200),
    status int DEFAULT 0,
    CONSTRAINT pkey_servers PRIMARY KEY (id),
    CONSTRAINT fkey_servers_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
) engine=InnoDB;


-- Table Zones
CREATE TABLE IF NOT EXISTS zones (
    id serial,
    domain varchar(255) NOT NULL,
    config text,
    gid bigint unsigned NOT NULL default 1,
    status int,
    server_id bigint unsigned NOT NULL, 
    master_id bigint unsigned NOT NULL,
    CONSTRAINT pkey_zones PRIMARY KEY (id,domain,server_id),
    CONSTRAINT fkey_zones_server FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE,
    CONSTRAINT fkey_zones_master FOREIGN KEY (master_id) REFERENCES servers(id) ON DELETE CASCADE
) engine=InnoDB;


-- Table hosts
CREATE TABLE IF NOT EXISTS hosts (
    id serial,
    oid bigint unsigned NOT NULL,
    tag varchar(200) NOT NULL UNIQUE,
    ip varchar(250),
    created timestamp DEFAULT CURRENT_TIMESTAMP,
    last_updated timestamp,
    gid bigint unsigned NOT NULL DEFAULT 1,
    rtype bigint unsigned NOT NULL DEFAULT 1,
    rid bigint unsigned default null,
    zone_id bigint unsigned NOT NULL DEFAULT 1,
    ttl int default 12,
    CONSTRAINT pkey_hosts PRIMARY KEY (id),
    CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
    CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_host_gid   FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_rtype FOREIGN KEY (rtype) REFERENCES record_types(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_zone  FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_rid   FOREIGN KEY (rid) REFERENCES hosts(id) ON DELETE CASCADE ON UPDATE CASCADE
) engine=InnoDB;


-- Table site ACL
CREATE TABLE IF NOT EXISTS site_acl(
    m varchar(200),
    z varchar(200),
    op varchar(200),
    auth_level int NOT NULL DEFAULT 100,
    tag character varying(200) default NULL,
    CONSTRAINT pkey_site_acl PRIMARY KEY(m,z,op)
) engine=InnoDB;


-- EO Table definitions


-- BEGIN DUMP DATA

-- Roles
INSERT INTO roles (tag,auth_level,description)
 values 
    ('admin', 100 , 'Administration rol'),
    ('advanced', 50, 'Advanced user'),
    ('standar', 1, 'Standard user');

-- GROUPS
INSERT INTO groups (tag,description)
 values
    ('all', 'DEFAULT group');


-- DEFAULT SITE_ACL
INSERT INTO site_acl (m,z,op,auth_level,tag)
 values
    ('','','',0,'Index'),
    ('','api','',0,'Api'),
    ('','cliupdate','',0,''),
    ('','downloads','',0,'Descargas'),
    ('','header','',0,'Header'),
    ('','ip','',0,'Muestra la Ip'),
    ('','logout','',0,'Desconexi&oacuten'),
    ('','main','',0,'Menu lateral'),
    ('','rest_host','',0,'No tienes acceso'),
    ('','contact','',0,'Contactanos'),
    ('usr','','',1,''),
    ('usr','hosts','',1,'Gestor de etiquetas'),
    ('usr','hosts','mod',1,''),
    ('usr','hosts','rq_mod',1,''),
    ('usr','hosts','rq_new',1,''),
    ('usr','hosts','rq_del',1,''),
    ('usr','users','login',0,'P&aacutegina de acceso'),
    ('usr','users','mod',1, 'Mi cuenta'),
    ('usr','users','remember',0,'Olvid&oacute contraseña'),
    ('usr','users','resetpass',0,'Cambiar contraseña'),
    ('usr','users','rq_login',0,''),
    ('usr','users','rq_mod',1,''),
    ('usr','users','rq_resetpass',0,''),
    ('usr','users','rq_signin',0,''),
    ('usr','users','sendtoken',0,''),
    ('adm','','',100,'Panel de administraci&oacuten'),
    ('adm','site','',100,'Panel de administraci&oacuten del sitio'),
    ('adm','site','manager',100,''),
    ('adm','service','',100,'Centro de administraci&oacuten'),
    ('adm','service','manager',100,'Estados de los servicios'),
    ('adm','servers','',100,'Muestra los servidores'),
    ('adm','servers','status',100,'Estado del servicio'),
    ('adm','zones','',100,''),
    ('cms','','',0,''),
    ('','ajax','',0,'Ajax');

-- RECORD_TYPES
INSERT INTO record_types(tag,description,auth_level)
 values
	('A','A register type',0),
	('NS','NS register type',0),
	('CNAME','CNAME register type',0),
	('MX','MX register type',0);
