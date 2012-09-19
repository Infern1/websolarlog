# SQLiteManager Dump
# Version: 1.2.4
# http://www.sqlitemanager.org/
#
# Host: localhost
# Generation Time: Wednesday 19th 2012f September 2012 06:29 am
# SQLite Version: 3.7.7.1
# PHP Version: 5.3.13
# Database: wsl.sdb
# --------------------------------------------------------

#
# Table structure for table: ConfigInv
#
CREATE TABLE 'ConfigInv' ( 'INV' INTEGER , 'YMAX' INTEGER , 'YINTERVAL' INTEGER , 'PRODXDAYS' INTEGER , 'LOCATION' VARCHAR , 'LATITUDE' VARCHAR , 'LONGITUDE' VARCHAR , 'PANELS1' VARCHAR , 'ROOF_ORIENTATION1' INTEGER , 'ROOF_PICTH1' INTEGER , 'PANELS2' VARCHAR , 'ROOF_ORIENTATION2' INTEGER , 'ROOF_PICTH2' INTEGER , 'EXPECTEDPROD' INTEGER , 'EXPECTJAN' DECIMAL , 'EXPECTFEB' DECIMAL , 'EXPECTMAR' DECIMAL , 'EXPECTAPR' DECIMAL , 'EXPECTMAY' DECIMAL , 'EXPECTJUN' DECIMAL , 'EXPECTJUI' DECIMAL , 'EXPECTAUG' DECIMAL , 'EXPECTSEP' DECIMAL , 'EXPECTOCT' DECIMAL , 'EXPECTNOV' DECIMAL , 'EXPECTDEC' DECIMAL );
# --------------------------------------------------------


#
# Table structure for table: ConfigMain
#
CREATE TABLE 'ConfigMain' ( 'PORT' VARCHAR , 'COMOPTION' VARCHAR , 'DEBUG' BOOLEAN , 'SYNC' BOOLEAN , 'NUMINV' INTEGER , 'AUTOMODE' BOOLEAN , 'LATITUDE' VARCHAR , 'LONGITUDE' VARCHAR , 'TITLE' VARCHAR , 'SUBTITLE' VARCHAR , 'SENDALARMS' BOOLEAN , 'SENDMSGS' BOOLEAN , 'FILTER' VARCHAR , 'EMAIL' VARCHAR , 'KEEPDDAYS' INTEGER , 'AMOUNTLOG' INTEGER , 'PVOUTPUT' BOOLEAN , 'PVOAPIKEY' VARCHAR , 'PVOSYSID' VARCHAR );
# --------------------------------------------------------


#
# Table structure for table: Energy
#
CREATE TABLE 'Energy' ( 'id' INTEGER PRIMARY KEY, 'INV' INTEGER , 'SDTE' VARCHAR , 'KWH' DECIMAL , `KWHT` INTEGER);
# --------------------------------------------------------


#
# Table structure for table: Event
#
CREATE TABLE 'Event' ( 'id' INTEGER PRIMARY KEY, 'INV' TEXT, 'SDTE' VARCHAR, 'Type' VARCHAR, 'Event' TEXT );
# --------------------------------------------------------


#
# Table structure for table: History
#
CREATE TABLE 'History' ( 'id' INTEGER PRIMARY KEY, 'SDTE' VARCHAR(30), 'INV' INTEGER(2), 'I1V' DECIMAL, 'I1A' DECIMAL, 'I1P' DECIMAL, 'I2V' DECIMAL, 'I2A' DECIMAL, 'I2P' DECIMAL, 'GV' DECIMAL, 'GA' DECIMAL, 'GP' DECIMAL, 'FRQ' DECIMAL, 'EFF' DECIMAL, 'INVT' DECIMAL, 'BOOT' DECIMAL, 'KWHT' DECIMAL, 'pmaxotd' DECIMAL );
# --------------------------------------------------------


#
# Table structure for table: Live
#
CREATE TABLE 'Live' ( 'id' INTEGER PRIMARY KEY, 'SDTE' VARCHAR(30) , 'INV' INTEGER(2), 'I1V' DECIMAL, 'I1A' DECIMAL, 'I1P' DECIMAL, 'I2V' DECIMAL, 'I2A' DECIMAL, 'I2P' DECIMAL, 'GV' DECIMAL, 'GA' DECIMAL, 'GP' DECIMAL, 'FRQ' DECIMAL, 'EFF' DECIMAL, 'INVT' DECIMAL, 'BOOT' DECIMAL, 'KWHT' DECIMAL , `datetime` TEXT);
CREATE INDEX Live_INV ON 'Live'('INV');
# --------------------------------------------------------


#
# Table structure for table: Lock
#
CREATE TABLE 'Lock' ( 'id' INTEGER PRIMARY KEY, 'INV' INTEGER, 'SDTE' DATETIME, 'Type' VARCHAR );
# --------------------------------------------------------


#
# Table structure for table: Pmaxotd
#
CREATE TABLE 'Pmaxotd' ( 'id' INTEGER PRIMARY KEY, 'INV' DECIMAL, 'SDTE' VARCHAR, 'GP' DECIMAL );
# --------------------------------------------------------

