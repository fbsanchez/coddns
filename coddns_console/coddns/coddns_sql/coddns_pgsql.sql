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
    id int NOT NULL,
    tag character varying(200) NOT NULL,
    auth_level int NOT NULL DEFAULT 1,
    description text,
    CONSTRAINT pkey_roles PRIMARY KEY (id)
);

-- Sequence for Roles
CREATE SEQUENCE roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY roles.id;


-- Table users
CREATE TABLE IF NOT EXISTS users (
    id int NOT NULL,
    mail character varying(250) NOT NULL,
    pass text NOT NULL,
    first_login timestamp DEFAULT now(),
    last_login timestamp,
    ip_last_login int,
    ip_first_login int,
    hash character varying(255),
    max_time_valid_hash timestamp,
    rol int unsigned,
    CONSTRAINT pkey_users PRIMARY KEY (id),
    CONSTRAINT fkey_users_rol FOREIGN KEY (rol) REFERENCES roles(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT const_usuarios_unique_mail UNIQUE (mail),
    CONSTRAINT users_hash_key UNIQUE (hash)
);

-- Sequence for Users
CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY users.id;


-- Table groups
CREATE TABLE IF NOT EXISTS groups (
    id int NOT NULL,
    tag character varying(200) NOT NULL,
    description text,
    parent int unsigned,
    CONSTRAINT pkey_groups PRIMARY KEY (id),
    CONSTRAINT fkey_group_parent FOREIGN KEY (parent) REFERENCES groups(id) ON DELETE CASCADE
);

-- Sequence for Users
CREATE SEQUENCE groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY groups.id;


-- Table object_types
CREATE TABLE IF NOT EXISTS object_types (
    id int NOT NULL,
    tag character varying(200) NOT NULL,
    description text,
    CONSTRAINT pkey_object_types PRIMARY KEY (id)
);

-- Sequence for object_types
CREATE SEQUENCE object_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY object_types.id;


-- Table tusers_groups
CREATE TABLE IF NOT EXISTS tusers_groups (
    id int NOT NULL,
    gid int unsigned NOT NULL,
    oid int unsigned NOT NULL,
    view  int(1) NOT NULL DEFAULT 0,
    edit  int(1) NOT NULL DEFAULT 0,
    admin int(1) NOT NULL DEFAULT 0,
    obj_type int DEFAULT null,
    CONSTRAINT pkey_user_group PRIMARY KEY (id),
    CONSTRAINT fkey_user_group_users FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_user_group_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
);

-- Sequence for tusers_groups
CREATE SEQUENCE object_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY tusers_groups.id;


-- Table Record types
CREATE TABLE IF NOT EXISTS record_types (
    id int NOT NULL,
    tag character varying(200) NOT NULL,
    description text,
    auth_level int NOT NULL DEFAULT 100
);

-- Sequence for record_types
CREATE SEQUENCE record_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY record_types.id;


-- Table Servers
CREATE TABLE IF NOT EXISTS servers (
    id int NOT NULL,
    tag character varying(200) NOT NULL default "default" UNIQUE,
    ip int,
    gid int unsigned NOT NULL default 1,
    user character varying(200),
    password text,
    config text,
    config_md5 character varying(200),
    status int DEFAULT 0,
    CONSTRAINT pkey_servers PRIMARY KEY (id),
    CONSTRAINT fkey_servers_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
);

-- Sequence for servers
CREATE SEQUENCE servers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY servers.id;


-- Table Zones
CREATE TABLE IF NOT EXISTS zones (
    id int NOT NULL,
    domain character varying(255) NOT NULL,
    config text,
    gid int unsigned NOT NULL,
    status int,
    server_id int unsigned NOT NULL, 
    master_id int unsigned NOT NULL,
    CONSTRAINT pkey_zones PRIMARY KEY (id,domain,server_id),
    CONSTRAINT fkey_zones_server FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE,
    CONSTRAINT fkey_zones_master FOREIGN KEY (master_id) REFERENCES servers(id) ON DELETE CASCADE
);

-- Sequence for zones
CREATE SEQUENCE zones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY zones.id;

-- Table hosts
CREATE TABLE IF NOT EXISTS hosts (
    id int NOT NULL,
    oid int unsigned NOT NULL,
    tag character varying(200) NOT NULL UNIQUE,
    ip character varying(250),
    created timestamp DEFAULT now(),
    last_updated timestamp,
    gid int unsigned NOT NULL DEFAULT 1,
    rtype int unsigned NOT NULL DEFAULT 1,
    rid int unsigned default null,
    zone_id int unsigned NOT NULL DEFAULT 1,
    ttl int default 12,
    CONSTRAINT pkey_hosts PRIMARY KEY (id),
    CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
    CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_host_gid   FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_rtype FOREIGN KEY (rtype) REFERENCES record_types(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_zone  FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fkey_host_rid   FOREIGN KEY (rid) REFERENCES hosts(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Sequence for hosts
CREATE SEQUENCE hosts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
    OWNED BY hosts.id;


-- Table site ACL
CREATE TABLE IF NOT EXISTS site_acl(
    m character varying(200),
    z character varying(200),
    op character varying(200),
    auth_level int NOT NULL DEFAULT 100,
    CONSTRAINT pkey_site_acl PRIMARY KEY(m,z,op)
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
    ('all', 'DEFAULT group');


-- DEFAULT SITE_ACL
INSERT INTO site_acl (m,z,op,auth_level)
 values
    ('','','',0),
    ('','api','',0),
    ('','cliupdate','',0),
    ('','downloads','',0),
    ('','header','',0),
    ('','ip','',0),
    ('','logout','',0),
    ('','main','',0),
    ('','rest_host','',0),
    ('usr','','',1),
    ('usr','hosts','',1),
    ('usr','hosts','mod',1),
    ('usr','hosts','rq_mod',1),
    ('usr','hosts','rq_new',1),
    ('usr','hosts','rq_del',1),
    ('usr','users','login',0),
    ('usr','users','mod',1),
    ('usr','users','remember',0),
    ('usr','users','resetpass',0),
    ('usr','users','rq_login',0),
    ('usr','users','rq_mod',1),
    ('usr','users','rq_resetpass',0),
    ('usr','users','rq_signin',0),
    ('usr','users','sendtoken',0),
    ('adm','','',100),
    ('adm','site','',100),
    ('adm','site','manager',100),
    ('adm','service','',100),
    ('adm','service','manager',100),
    ('cms','','',0),
    ('ajax','','',0);

-- RECORD_TYPES
INSERT INTO record_types(tag,description,auth_level)
 values
    ('A','A register type',0),
    ('NS','NS register type',0),
    ('CNAME','CNAME register type',0),
    ('MX','MX register type',0);
