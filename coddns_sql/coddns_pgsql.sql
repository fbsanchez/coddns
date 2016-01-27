-- 
-- SQL script for CODDNS system
-- 
create database db_ddnsp;
GRANT ALL ON DATABASE db_ddnsp TO postgres;
\c db_ddnsp;
SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
create schema if not exists sch_ddnsp;
SET search_path TO sch_ddnsp,public;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres; Tablespace:
--

CREATE TABLE IF NOT EXISTS users (
    id int NOT NULL,
    mail character varying(250) NOT NULL,
    pass text NOT NULL,
    last_login timestamp with time zone,
    first_login timestamp with time zone DEFAULT now(),
    ip_last_login int,
    ip_first_login int,
    hash text,
    max_time_valid_hash timestamp with time zone,
	CONSTRAINT pkey_users PRIMARY KEY (id),
	CONSTRAINT const_users_unique_mail UNIQUE (mail),
	CONSTRAINT users_hash_key UNIQUE (hash)

);


--
-- Name: hosts; Type: TABLE; Schema: sch_ddnsp; Owner: postgres; Tablespace:
--

CREATE TABLE IF NOT EXISTS hosts (
    id int NOT NULL,
    oid int NOT NULL,
    tag character varying(200) NOT NULL,
    ip int,
    created timestamp with time zone DEFAULT now(),
    last_updated timestamp with time zone DEFAULT now(),
	CONSTRAINT pkey_host PRIMARY KEY (id),
	CONSTRAINT const_hosts_unique_tag UNIQUE (tag),
	CONSTRAINT fkey_host_owner FOREIGN KEY (oid) REFERENCES users(id) ON DELETE CASCADE
);

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: sch_ddnsp; Owner: postgres
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
	OWNED BY users.id;

	--
-- Name: hosts_id_seq; Type: SEQUENCE; Schema: sch_ddnsp; Owner: postgres
--

CREATE SEQUENCE hosts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
	OWNED BY hosts.id;

--
-- Name: id; Type: DEFAULT; Schema: sch_ddnsp; Owner: postgres
--
ALTER TABLE ONLY hosts ALTER COLUMN id SET DEFAULT nextval('hosts_id_seq'::regclass);

--
-- Name: id; Type: DEFAULT; Schema: sch_ddnsp; Owner: postgres
--
ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);

-- 
-- GRANTS
--
GRANT ALL ON SCHEMA sch_ddnsp TO postgres;
GRANT ALL ON TABLE hosts TO postgres;
GRANT ALL ON TABLE users TO postgres;


