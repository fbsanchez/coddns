-- 
-- SQL script for CODDNS system
-- 
SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;


-- Table roles
CREATE TABLE IF NOT EXISTS roles (
    id serial,
    tag character varying(200) NOT NULL,
    auth_level int NOT NULL DEFAULT 1,
    description text,
    CONSTRAINT pkey_roles PRIMARY KEY (id)
);

-- Table users
CREATE TABLE IF NOT EXISTS users (
    id serial,
    mail character varying(250) NOT NULL,
    pass text NOT NULL,
    first_login timestamp DEFAULT now(),
    last_login timestamp,
    ip_last_login bigint,
    ip_first_login bigint,
    hash character varying(255),
    max_time_valid_hash timestamp,
    rol int,
    CONSTRAINT pkey_users PRIMARY KEY (id),
    CONSTRAINT fkey_users_rol FOREIGN KEY (rol) REFERENCES roles(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT const_usuarios_unique_mail UNIQUE (mail),
    CONSTRAINT users_hash_key UNIQUE (hash)
);

-- Table groups
CREATE TABLE IF NOT EXISTS groups (
    id serial,
    tag character varying(200) NOT NULL,
    description text,
    parent int,
    CONSTRAINT pkey_groups PRIMARY KEY (id),
    CONSTRAINT fkey_group_parent FOREIGN KEY (parent) REFERENCES groups(id) ON DELETE CASCADE
);

-- Table object_types
CREATE TABLE IF NOT EXISTS object_types (
    id serial,
    tag character varying(200) NOT NULL,
    description text,
    CONSTRAINT pkey_object_types PRIMARY KEY (id)
);

-- Table tusers_groups
CREATE TABLE IF NOT EXISTS tusers_groups (
    id serial,
    gid int NOT NULL default 1,
    oid int NOT NULL,
    view  smallint NOT NULL DEFAULT 0,
    edit  smallint NOT NULL DEFAULT 0,
    admin smallint NOT NULL DEFAULT 0,
    obj_type int DEFAULT null,
    CONSTRAINT pkey_user_group PRIMARY KEY (id),
    CONSTRAINT fkey_user_group_users FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_user_group_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
);

-- Table Record types
CREATE TABLE IF NOT EXISTS record_types (
    id serial,
    tag character varying(200) NOT NULL,
    description text,
    auth_level int NOT NULL DEFAULT 100,
    CONSTRAINT pkey_record_types PRIMARY KEY (id)
);

-- Table Servers
CREATE TABLE IF NOT EXISTS servers (
    id serial,
    tag character varying(200) NOT NULL default 'default' UNIQUE,
    ip bigint,
    gid int NOT NULL default 1,
    port int default 22,
    gid bigint unsigned NOT NULL default 1,
    srv_user character varying(200),
    srv_password text,
    main_config_file character varying(255) default "/etc/named.conf",
    fingerprint text,
    status int DEFAULT 0,
    pub_key_file character varying(255) default NULL,
    priv_key_file character varying(255) default NULL,
    last_update timestamp,
    CONSTRAINT pkey_servers PRIMARY KEY (id),
    CONSTRAINT fkey_servers_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
);

-- Table Zones
CREATE TABLE IF NOT EXISTS zones (
    id serial UNIQUE,
    domain character varying(255) NOT NULL,
    config text,
    gid int NOT NULL default 1,
    is_public int(1) unsigned NOT NULL default 0,
    status int,
    server_id int NOT NULL, 
    master_id int NOT NULL,
    CONSTRAINT pkey_zones PRIMARY KEY (id,domain,server_id),
    CONSTRAINT fkey_zones_server FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE,
    CONSTRAINT fkey_zones_master FOREIGN KEY (master_id) REFERENCES servers(id) ON DELETE CASCADE
);

-- Table hosts
CREATE TABLE IF NOT EXISTS hosts (
    id serial,
    oid int NOT NULL,
    tag character varying(200) NOT NULL UNIQUE,
    ip character varying(250),
    created timestamp DEFAULT now(),
    last_updated timestamp,
    gid int NOT NULL DEFAULT 1,
    rtype int NOT NULL DEFAULT 1,
    rid int default null,
    zone_id int NOT NULL DEFAULT 1,
    ttl int default 12,
    CONSTRAINT pkey_hosts PRIMARY KEY (id),
    CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
    CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_host_gid   FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_rtype FOREIGN KEY (rtype) REFERENCES record_types(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_zone  FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_rid   FOREIGN KEY (rid) REFERENCES hosts(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table SZB
CREATE TABLE IF NOT EXISTS stats_szb (
    id_block serial,
    id_server bigint DEFAULT NULL,
    id_zone bigint DEFAULT NULL,
    tag character varying(250),
    CONSTRAINT pkey_stats_szb PRIMARY KEY (id_block,tag)
);


-- Table stats_item
CREATE TABLE IF NOT EXISTS stats_item (
    id serial,
    id_block bigint unsigned NOT NULL,
    tag character varying(250),
    CONSTRAINT pkey_stats_stats_item PRIMARY KEY (id),
    CONSTRAINT fkey_stats_item_block FOREIGN KEY (id_block) REFERENCES stats_szb(id_block) ON DELETE CASCADE ON UPDATE CASCADE
);


-- Table stats_data
CREATE TABLE IF NOT EXISTS stats_data (
    value int,
    utimestamp int,
    id_item bigint unsigned NOT NULL,
    CONSTRAINT pkey_stats_stats_data PRIMARY KEY (utimestamp,id_item),
    CONSTRAINT fkey_stats_data_item FOREIGN KEY (id_item) REFERENCES stats_item(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table site ACL
CREATE TABLE IF NOT EXISTS site_acl(
    m character varying(200),
    z character varying(200),
    op character varying(200),
    tag character varying(200) default NULL,
    auth_level int NOT NULL DEFAULT 100,
    CONSTRAINT pkey_site_acl PRIMARY KEY(m,z,op)
);

-- Auxiliary tables

-- Table site ACL
CREATE TABLE IF NOT EXISTS site_acl(
    m character varying(200),
    z character varying(200),
    op character varying(200),
    auth_level int NOT NULL DEFAULT 100,
    tag character varying(200) default NULL,
    CONSTRAINT pkey_site_acl PRIMARY KEY(m,z,op)
);


-- Table versioning
CREATE TABLE IF NOT EXISTS versioning(
    id serial,
    filepath character varying(1024),
    original_filepath character varying(255),
    created timestamp DEFAULT now(),
    description text,
    CONSTRAINT pkey_versioning PRIMARY KEY(id,original_filepath)
);


-- Table settings
CREATE TABLE IF NOT EXISTS settings(
    id serial,
    field character varying(255) NOT NULL UNIQUE,
    value text,
    CONSTRAINT pkey_settings PRIMARY KEY(id,field)
);

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
    ('all', 'Global group'),
    ('private', 'DEFAULT group');


-- DEFAULT SITE_ACL
INSERT INTO site_acl (m,z,op,auth_level)
 values
    ('','','',0,'Index'),
    ('','api','',0,'Api'),
    ('','cliupdate','',0,''),
    ('','downloads','',0,'Descargas'),
    ('','header','',0,'Header'),
    ('','ip','',0,'Muestra la Ip'),
    ('','main','',0,'Menu lateral'),
    ('','rest_host','',0,'No tienes acceso'),
    ('','contact','',0,'Contactanos'),
    ('','ajax','',0,'Ajax'),
    ('','err404','',0,'Ajax'),
    ('usr','','',1,''),
    ('usr','hosts','',1,'Gestor de etiquetas'),
    ('usr','hosts','mod',1,''),
    ('usr','hosts','rq_mod',1,''),
    ('usr','hosts','rq_new',1,''),
    ('usr','hosts','rq_del',1,''),
    ('usr','users','login',0,'P&aacute;gina de acceso'),
    ('usr','users','mod',1, 'Mi cuenta'),
    ('usr','users','remember',0,'Olvid&oacute; contraseña'),
    ('usr','users','resetpass',0,'Cambiar contraseña'),
    ('usr','users','rq_login',0,''),
    ('usr','users','rq_mod',1,''),
    ('usr','users','rq_resetpass',0,''),
    ('usr','users','rq_signin',0,''),
    ('usr','users','sendtoken',0,''),
    ('usr','users','logout',0,'Desconexi&oacute;n'),
    ('adm','','',100,'Panel de administraci&oacute;n'),
    ('adm','site','',100,'Panel de administraci&oacute;n del sitio'),
    ('adm','site','manager',100,'Administraci&oacute;n del sitio'),
    ('adm','site','rq_new_user',100,'Crear un nuevo usuario'),
    ('adm','center','',100,'Centro de administraci&oacute;n'),
    ('adm','service','status',100,'Estado del servicios'),
    ('adm','service','manager',100,'Administraci&oacute;n del servicios'),
    ('adm','servers','',100,'Administraci&oacute;n de servidores'),
    ('adm','server','status',100,'Estado del servidor'),
    ('adm','server','control',100,'Control del servidor'),
    ('adm','server','manager',100,'Centro de configuraci&oacute;n de servidores'),
    ('adm','server','settings_manager',100,'Editor de configuraci&oacute;n de servidores'),
    ('adm','server','rq_settings_manager',100,'Receptor de formulario de configuraci&oacute;n del servidor'),
    ('adm','server','new',100,'Formulario nuevo servidor'),
    ('adm','server','rq_new',100,'Receptor de formulario nuevo servidor'),
    ('adm','zones','',100,'Administraci&oacute;n de zonas'),
    ('cms','','',0,'Documentaci&oacute;n');

-- RECORD_TYPES
INSERT INTO record_types(tag,description,auth_level)
 values
    ('A','A register type',0),
    ('NS','NS register type',0),
    ('CNAME','CNAME register type',0),
    ('MX','MX register type',0);


-- SETTINGS - DEFAULT
INSERT INTO settings(field,value)
 values
    ("slack_url", ""),
    ("installdir", "/opt/coddns/"),
    ("spooldir", "/opt/coddns/spool/"),
    ("max_age", "7"),
    ("rndc_key", "/share/ddns/rndc.key");

