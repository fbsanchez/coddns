-- 
-- SQL script for CODDNS system
-- 
-- create database db_ddnsp;
-- GRANT ALL PRIVILEGES ON db_ddnsp.* TO ddnsp@'127.0.0.1' identified by 'p4ssw0rd';



CREATE TABLE IF NOT EXISTS users (
    id int NOT NULL auto_increment,
    mail varchar(250) NOT NULL,
    pass text NOT NULL default "",
    first_login timestamp DEFAULT CURRENT_TIMESTAMP,
    last_login timestamp,
    ip_last_login int,
    ip_first_login int,
    hash varchar(255),
    max_time_valid_hash timestamp,
	CONSTRAINT pkey_users PRIMARY KEY (id),
	CONSTRAINT const_usuarios_unique_mail UNIQUE (mail),
	CONSTRAINT users_hash_key UNIQUE (hash)
) engine=InnoDB;


CREATE TABLE IF NOT EXISTS hosts (
    id serial NOT NULL,
    oid int NOT NULL,
    tag varchar(200) NOT NULL,
    ip int,
    created timestamp DEFAULT CURRENT_TIMESTAMP,
    last_updated timestamp,
	CONSTRAINT pkey_hosts PRIMARY KEY (id),
	CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
	CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE
) engine=InnoDB;



