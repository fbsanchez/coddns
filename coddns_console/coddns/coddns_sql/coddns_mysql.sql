-- 
-- SQL script for CODDNS system
-- 
-- create database db_ddnsp;
-- GRANT ALL PRIVILEGES ON db_ddnsp.* TO ddnsp@'127.0.0.1' identified by 'p4ssw0rd';

-- Table roles
CREATE TABLE IF NOT EXISTS roles (
    id serial,
    tag varchar(200) NOT NULL,
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
    CONSTRAINT pkey_groups PRIMARY KEY (id)
) engine=InnoDB;


-- Table tusers_groups
CREATE TABLE IF NOT EXISTS tusers_groups (
    id serial,
    gid bigint unsigned NOT NULL,
    oid bigint unsigned NOT NULL,
    parent bigint unsigned NOT NULL,
    view  int(1) NOT NULL default 0,
    edit  int(1) NOT NULL default 0,
    admin int(1) NOT NULL default 0,
    CONSTRAINT pkey_user_group PRIMARY KEY (id),
    CONSTRAINT fkey_user_group_users FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_user_group_group FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE,
    CONSTRAINT fkey_user_group_ug FOREIGN KEY (parent) REFERENCES tusers_groups(id) ON DELETE CASCADE
) engine=InnoDB;


-- Table hosts
CREATE TABLE IF NOT EXISTS hosts (
    id serial,
    oid bigint unsigned NOT NULL,
    tag varchar(200) NOT NULL,
    ip int,
    created timestamp DEFAULT CURRENT_TIMESTAMP,
    last_updated timestamp,
    gid bigint unsigned NOT NULL,
    CONSTRAINT pkey_hosts PRIMARY KEY (id),
    CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
    CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fkey_host_gid FOREIGN KEY (gid) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE
) engine=InnoDB;


-- EO Table definitions


-- BEGIN DUMP DATA

-- Roles
INSERT INTO roles (tag,description)
 values 
    ('admin', 'Administration rol'),
    ('advanced','Advanced user'),
    ('standar','Standard user');

-- GROUPS
INSERT INTO groups (tag,description)
 values
    ('all', 'Default group');