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


-- Table tusers_groups
CREATE TABLE IF NOT EXISTS tusers_groups (
    id serial,
    gid bigint unsigned NOT NULL,
    oid bigint unsigned NOT NULL,
    view  int(1) NOT NULL DEFAULT 0,
    edit  int(1) NOT NULL DEFAULT 0,
    admin int(1) NOT NULL DEFAULT 0,
    CONSTRAINT pkey_user_group PRIMARY KEY (id),
    CONSTRAINT fkey_user_group_users FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_user_group_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE
) engine=InnoDB;


-- Table hosts
CREATE TABLE IF NOT EXISTS hosts (
    id serial,
    oid bigint unsigned NOT NULL,
    tag varchar(200) NOT NULL,
    ip int,
    created timestamp DEFAULT CURRENT_TIMESTAMP,
    last_updated timestamp,
    gid bigint unsigned NOT NULL DEFAULT 1,
    CONSTRAINT pkey_hosts PRIMARY KEY (id),
    CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
    CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_host_gid FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE
) engine=InnoDB;


-- Table site ACL
CREATE TABLE IF NOT EXISTS site_acl(
    m varchar(200),
    z varchar(200),
    op varchar(200),
    auth_level int NOT NULL DEFAULT 100,
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
    ('cms','','',0);
