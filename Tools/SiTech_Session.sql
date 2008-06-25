CREATE TABLE SiTech_Sessions (
  Name varchar(20) NOT NULL default '',
  Id varchar(32) NOT NULL default '',
  Data tinytext NOT NULL,
  Started timestamp NOT NULL default CURRENT_TIMESTAMP,
  Remember tinyint(1) NOT NULL default '0',
  Strict tinyint(1) NOT NULL default '0',
  RemoteAddr varchar(15) NOT NULL default '',
  PRIMARY KEY  (Name,Id)
);
