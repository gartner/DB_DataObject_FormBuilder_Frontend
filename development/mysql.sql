-- Create tables for mysql

CREATE TABLE IF NOT EXISTS categories (
  id integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
  name varchar(75),
  description text
) engine 'innoDB';

CREATE TABLE IF NOT EXISTS pictures (
  id integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
  mimetype varchar(25),
  title varchar(75) NOT NULL,
  date_taken date,
  description text,
  width integer NOT NULL,
  height integer NOT NULL,
  added timestamp DEFAULT now(),
  lastupdate timestamp
) engine 'innoDB';

CREATE TABLE IF NOT EXISTS pictures_categories (
   id integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
   category_id integer REFERENCES categories ON UPDATE CASCADE ON DELETE CASCADE,
   picture_id integer REFERENCES pictures ON UPDATE CASCADE ON DELETE CASCADE
) engine 'innoDB';

CREATE TABLE IF NOT EXISTS pages (
  id integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
  title varchar(75) NOT NULL,
  content text,
  keywords text
) engine 'innoDB';
