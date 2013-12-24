-- MySQL dump 10.9
--
-- Host: localhost    Database: aaa
-- ------------------------------------------------------
-- Server version	4.1.22-community-nt
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `oun_account_log`
--

DROP TABLE IF EXISTS `oun_account_log`;
CREATE TABLE `oun_account_log` (
  `log_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `type` varchar(30) NOT NULL default '',
  `change_desc` varchar(255) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`log_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=126;

--
-- Table structure for table `oun_ad`
--

DROP TABLE IF EXISTS `oun_ad`;
CREATE TABLE `oun_ad` (
  `ad_id` smallint(5) unsigned NOT NULL auto_increment,
  `position_id` smallint(5) unsigned NOT NULL default '0',
  `user_id` int(5) unsigned NOT NULL default '0',
  `media_type` tinyint(3) unsigned NOT NULL default '0',
  `ad_name` varchar(60) NOT NULL default '',
  `ad_link` varchar(255) NOT NULL default '',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `link_man` varchar(60) NOT NULL default '',
  `link_email` varchar(60) NOT NULL default '',
  `link_phone` varchar(60) NOT NULL default '',
  `click_count` mediumint(8) unsigned NOT NULL default '0',
  `enabled` tinyint(3) unsigned NOT NULL default '1',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`),
  KEY `domain_id` (`domain_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_ad_affiche`
--

DROP TABLE IF EXISTS `oun_ad_affiche`;
CREATE TABLE `oun_ad_affiche` (
  `aaid` mediumint(8) NOT NULL auto_increment,
  `ad_id` smallint(5) unsigned NOT NULL default '0',
  `y` varchar(4) NOT NULL default '',
  `m` varchar(2) NOT NULL default '',
  `d` varchar(2) NOT NULL default '',
  `adddate` int(11) NOT NULL default '0',
  `ip` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`aaid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_ad_position`
--

DROP TABLE IF EXISTS `oun_ad_position`;
CREATE TABLE `oun_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL auto_increment,
  `position_name` varchar(60) NOT NULL default '',
  `ad_width` smallint(5) unsigned NOT NULL default '0',
  `ad_height` smallint(5) unsigned NOT NULL default '0',
  `position_desc` varchar(255) NOT NULL default '',
  `position_style` text NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`position_id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=28;

--
-- Table structure for table `oun_admin_user`
--

DROP TABLE IF EXISTS `oun_admin_user`;
CREATE TABLE `oun_admin_user` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `user_name` varchar(30) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `add_time` int(11) NOT NULL default '0',
  `last_login` int(11) NOT NULL default '0',
  `last_ip` varchar(15) NOT NULL default '',
  `action_list` text NOT NULL,
  `articlecat_list` varchar(250) NOT NULL default '',
  `nav_list` text NOT NULL,
  `lang_type` varchar(50) NOT NULL default '',
  `agency_id` smallint(5) unsigned NOT NULL default '0',
  `praid` int(5) unsigned NOT NULL default '0',
  `todolist` longtext,
  `user_type` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `agency_id` (`agency_id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_arti_attr`
--

DROP TABLE IF EXISTS `oun_arti_attr`;
CREATE TABLE `oun_arti_attr` (
  `aaid` int(6) unsigned NOT NULL auto_increment,
  `attr_name` varchar(60) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`aaid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=19;

--
-- Table structure for table `oun_arti_comms`
--

DROP TABLE IF EXISTS `oun_arti_comms`;
CREATE TABLE `oun_arti_comms` (
  `arcid` mediumint(8) unsigned NOT NULL auto_increment,
  `arid` mediumint(8) NOT NULL default '0',
  `acid` int(6) unsigned NOT NULL default '0',
  `top` tinyint(1) NOT NULL default '0',
  `support` int(6) unsigned NOT NULL default '0',
  `against` int(6) unsigned NOT NULL default '0',
  `coms_type` int(1) NOT NULL default '0',
  `tel` varchar(23) NOT NULL default '',
  `pos` varchar(6) NOT NULL default '',
  `addrs` varchar(100) NOT NULL default '',
  `ip` varchar(16) NOT NULL default '',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `email` varchar(80) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `descs` text,
  `dateadd` int(11) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`arcid`),
  KEY `domain_id` (`domain_id`),
  KEY `arid` (`arid`),
  KEY `top` (`top`),
  KEY `user_id` (`user_id`),
  KEY `acid` (`acid`),
  KEY `type` (`coms_type`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_arti_comms_re`
--

DROP TABLE IF EXISTS `oun_arti_comms_re`;
CREATE TABLE `oun_arti_comms_re` (
  `arcrid` mediumint(8) unsigned NOT NULL auto_increment,
  `arcid` mediumint(8) NOT NULL default '0',
  `arid` mediumint(8) NOT NULL default '0',
  `descs` text,
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`arcrid`),
  KEY `domain_id` (`domain_id`),
  KEY `arid` (`arid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_arti_file`
--

DROP TABLE IF EXISTS `oun_arti_file`;
CREATE TABLE `oun_arti_file` (
  `fileid` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `arid` mediumint(8) unsigned NOT NULL default '0',
  `type` varchar(80) NOT NULL default '',
  `filename` varchar(100) NOT NULL default '',
  `thumb_url` varchar(100) NOT NULL default '',
  `descs` varchar(250) NOT NULL default '',
  `domain_id` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`fileid`),
  KEY `arid` (`arid`),
  KEY `domain_id` (`domain_id`),
  KEY `type` (`type`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_arti_tag`
--

DROP TABLE IF EXISTS `oun_arti_tag`;
CREATE TABLE `oun_arti_tag` (
  `atid` mediumint(8) unsigned NOT NULL auto_increment,
  `arid` mediumint(8) unsigned NOT NULL default '0',
  `art_pro_type` tinyint(1) NOT NULL default '0',
  `keys` varchar(30) NOT NULL default '',
  `top` int(3) NOT NULL default '0',
  `domain_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`atid`),
  KEY `domain_id` (`domain_id`),
  KEY `keys` (`keys`),
  KEY `top` (`top`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_articat`
--

DROP TABLE IF EXISTS `oun_articat`;
CREATE TABLE `oun_articat` (
  `acid` int(6) unsigned NOT NULL auto_increment,
  `fid` int(5) NOT NULL default '0',
  `next_node` varchar(80) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `keywords` varchar(250) NOT NULL default '',
  `ifshow` tinyint(1) NOT NULL default '1',
  `ifnav` tinyint(1) NOT NULL default '0',
  `vtid` mediumint(8) NOT NULL default '0',
  `utid` int(6) NOT NULL default '0',
  `allowjob` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`acid`),
  KEY `fid` (`fid`),
  KEY `domain_id` (`domain_id`),
  KEY `utid` (`utid`)
) TYPE=MyISAM AUTO_INCREMENT=54;

--
-- Table structure for table `oun_article`
--

DROP TABLE IF EXISTS `oun_article`;
CREATE TABLE `oun_article` (
  `arid` mediumint(8) unsigned NOT NULL default '0',
  `user_id` smallint(5) NOT NULL default '0',
  `sour` varchar(50) NOT NULL default '',
  `sourhttp` varchar(200) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `descs` text,
  `cltion` text NOT NULL,
  `cltion_product` text NOT NULL,
  `cltion_topic` text NOT NULL,
  `states` tinyint(1) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`arid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_artitxt`
--

DROP TABLE IF EXISTS `oun_artitxt`;
CREATE TABLE `oun_artitxt` (
  `arid` mediumint(8) unsigned NOT NULL auto_increment,
  `acid` smallint(5) NOT NULL default '0',
  `aaid` int(6) unsigned NOT NULL default '0',
  `vtid` mediumint(8) NOT NULL default '0',
  `ifpic` int(3) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `subname` varchar(50) NOT NULL default '',
  `otherurl` varchar(160) NOT NULL default '',
  `edit_comm` varchar(250) NOT NULL default '',
  `top` int(3) NOT NULL default '0',
  `focus` int(3) NOT NULL default '0',
  `trundle` tinyint(3) NOT NULL default '0',
  `colors` varchar(7) NOT NULL default '',
  `arti_date` int(11) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `user_id` int(6) unsigned NOT NULL default '0',
  `support` int(6) unsigned NOT NULL default '0',
  `against` int(6) unsigned NOT NULL default '0',
  `hots` int(6) unsigned NOT NULL default '0',
  `comms` int(5) NOT NULL default '0',
  `min_thumb` varchar(100) NOT NULL default '',
  `arti_thumb` varchar(100) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`arid`),
  KEY `states` (`states`),
  KEY `domain_id` (`domain_id`),
  KEY `edit_date` (`arti_date`),
  KEY `support` (`support`),
  KEY `against` (`against`),
  KEY `user_id` (`user_id`),
  KEY `hots` (`hots`),
  KEY `aaid` (`aaid`),
  KEY `focus` (`focus`),
  KEY `ifpic` (`ifpic`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_artitxt_ip`
--

DROP TABLE IF EXISTS `oun_artitxt_ip`;
CREATE TABLE `oun_artitxt_ip` (
  `aipid` int(8) unsigned NOT NULL auto_increment,
  `arid` int(8) unsigned NOT NULL default '0',
  `types` tinyint(1) unsigned NOT NULL default '0',
  `ip` varchar(40) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`aipid`),
  KEY `domain_id` (`domain_id`),
  KEY `arid` (`arid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_carts`
--

DROP TABLE IF EXISTS `oun_carts`;
CREATE TABLE `oun_carts` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL default '0',
  `prid` int(11) NOT NULL default '0',
  `nums` int(11) NOT NULL default '0',
  `sellprice` decimal(9,2) default NULL,
  `prices` decimal(9,2) NOT NULL default '0.00',
  `notes` varchar(250) default NULL,
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `dateadd` (`dateadd`),
  KEY `prid` (`prid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_citycat`
--

DROP TABLE IF EXISTS `oun_citycat`;
CREATE TABLE `oun_citycat` (
  `ccid` int(6) unsigned NOT NULL auto_increment,
  `fid` int(5) NOT NULL default '0',
  `next_node` varchar(80) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `allow` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ccid`),
  KEY `fid` (`fid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=453;

--
-- Table structure for table `oun_dds`
--

DROP TABLE IF EXISTS `oun_dds`;
CREATE TABLE `oun_dds` (
  `id` int(11) NOT NULL auto_increment,
  `ddid` varchar(20) NOT NULL default '',
  `stats` int(11) NOT NULL default '0',
  `users_id` int(11) NOT NULL default '0',
  `pronums` int(11) NOT NULL default '0',
  `totalmoney` decimal(10,2) NOT NULL default '0.00',
  `wlname` varchar(60) default NULL,
  `wlsn` varchar(50) default NULL,
  `wlpay` decimal(9,2) NOT NULL default '0.00',
  `freight` varchar(20) default NULL,
  `time` int(11) default NULL,
  `sh_name` varchar(15) NOT NULL default '',
  `sh_address` varchar(255) NOT NULL default '',
  `sh_zip` int(6) NOT NULL default '0',
  `sh_phone` varchar(20) NOT NULL default '',
  `notes` text,
  `payed` tinyint(1) NOT NULL default '0',
  `payedtime` int(11) default NULL,
  `fhid` int(11) default NULL,
  `domain_id` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ddid` (`ddid`),
  KEY `stats` (`stats`),
  KEY `users_id` (`users_id`),
  KEY `cbpay` (`wlpay`),
  KEY `payed` (`payed`),
  KEY `payedtime` (`payedtime`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_ddscarts`
--

DROP TABLE IF EXISTS `oun_ddscarts`;
CREATE TABLE `oun_ddscarts` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL default '0',
  `prid` int(11) NOT NULL default '0',
  `nums` int(11) NOT NULL default '0',
  `sellprice` decimal(9,2) default NULL,
  `prices` decimal(9,2) NOT NULL default '0.00',
  `totalprice` decimal(10,2) default NULL,
  `notes` varchar(250) default NULL,
  `dateadd` int(11) NOT NULL default '0',
  `ddid` varchar(20) NOT NULL default '1',
  `domain_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ddid` (`ddid`),
  KEY `dateadd` (`dateadd`),
  KEY `prid` (`prid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_filter`
--

DROP TABLE IF EXISTS `oun_filter`;
CREATE TABLE `oun_filter` (
  `fid` int(5) unsigned NOT NULL auto_increment,
  `ips` text,
  `words` text,
  `keysre` text NOT NULL,
  `states` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=2;

--
-- Table structure for table `oun_inducat`
--

DROP TABLE IF EXISTS `oun_inducat`;
CREATE TABLE `oun_inducat` (
  `inducatid` int(6) unsigned NOT NULL auto_increment,
  `fid` int(5) NOT NULL default '0',
  `next_node` varchar(80) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `allow` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`inducatid`),
  KEY `fid` (`fid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=32;

--
-- Table structure for table `oun_links`
--

DROP TABLE IF EXISTS `oun_links`;
CREATE TABLE `oun_links` (
  `lkid` int(8) unsigned NOT NULL auto_increment,
  `lk_name` varchar(60) NOT NULL default '',
  `lk_logo` varchar(80) NOT NULL default '',
  `lk_desc` text NOT NULL,
  `site_url` varchar(255) NOT NULL default '',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `colors` varchar(7) NOT NULL default '',
  `is_show` tinyint(1) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`lkid`),
  KEY `domain_id` (`domain_id`),
  KEY `is_show` (`is_show`),
  KEY `states` (`states`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_messages`
--

DROP TABLE IF EXISTS `oun_messages`;
CREATE TABLE `oun_messages` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` smallint(5) NOT NULL default '0',
  `username` varchar(20) NOT NULL default '',
  `touser_id` smallint(5) NOT NULL default '0',
  `tousername` varchar(20) NOT NULL default '',
  `title` varchar(250) NOT NULL default '',
  `descs` text NOT NULL,
  `dateadd` int(11) NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `restates` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `reuser_id` (`touser_id`),
  KEY `domain_id` (`domain_id`),
  KEY `dateadd` (`dateadd`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_messagesre`
--

DROP TABLE IF EXISTS `oun_messagesre`;
CREATE TABLE `oun_messagesre` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `messagesid` mediumint(8) NOT NULL default '0',
  `touser_id` smallint(5) NOT NULL default '0',
  `tousername` varchar(20) NOT NULL default '',
  `descs` text NOT NULL,
  `dateadd` int(11) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_messagesread`
--

DROP TABLE IF EXISTS `oun_messagesread`;
CREATE TABLE `oun_messagesread` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` smallint(5) NOT NULL default '0',
  `messagesid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `messagesid` (`messagesid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_nav`
--

DROP TABLE IF EXISTS `oun_nav`;
CREATE TABLE `oun_nav` (
  `id` mediumint(8) NOT NULL auto_increment,
  `ifbotton` tinyint(1) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `ifshow` tinyint(1) NOT NULL default '0',
  `vieworder` tinyint(1) NOT NULL default '0',
  `opennew` tinyint(1) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `url_logo` varchar(50) NOT NULL default '',
  `top` int(6) unsigned NOT NULL default '0',
  `domain_id` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ifshow` (`ifshow`),
  KEY `domain_id` (`domain_id`),
  KEY `url` (`url`),
  KEY `top` (`top`)
) TYPE=MyISAM AUTO_INCREMENT=1797;

--
-- Table structure for table `oun_prattcat`
--

DROP TABLE IF EXISTS `oun_prattcat`;
CREATE TABLE `oun_prattcat` (
  `pacid` smallint(5) unsigned NOT NULL auto_increment,
  `paname` varchar(60) NOT NULL default '',
  `enabled` tinyint(1) unsigned NOT NULL default '1',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pacid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_prattri`
--

DROP TABLE IF EXISTS `oun_prattri`;
CREATE TABLE `oun_prattri` (
  `paid` smallint(5) unsigned NOT NULL auto_increment,
  `pacid` smallint(5) unsigned NOT NULL default '0',
  `attr_name` varchar(40) NOT NULL default '',
  `attr_input_type` tinyint(1) unsigned NOT NULL default '1',
  `attr_values` text NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`paid`),
  KEY `pacid` (`pacid`),
  KEY `domain_id` (`domain_id`),
  KEY `sort_order` (`sort_order`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_prattrival`
--

DROP TABLE IF EXISTS `oun_prattrival`;
CREATE TABLE `oun_prattrival` (
  `pavid` mediumint(8) unsigned NOT NULL auto_increment,
  `paid` smallint(5) unsigned NOT NULL default '0',
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `pavals` varchar(100) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pavid`),
  KEY `domain_id` (`domain_id`),
  KEY `prid` (`prid`,`paid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail`
--

DROP TABLE IF EXISTS `oun_pravail`;
CREATE TABLE `oun_pravail` (
  `praid` int(5) unsigned NOT NULL auto_increment,
  `fid` int(6) NOT NULL default '0',
  `next_node` varchar(80) NOT NULL default '',
  `cotype` tinyint(1) NOT NULL default '0',
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `pra_name` varchar(80) NOT NULL default '',
  `shop_logo` varchar(80) NOT NULL default '',
  `pra_url` varchar(180) NOT NULL default '',
  `sets` text NOT NULL,
  `descs` text,
  `notices` text NOT NULL,
  `ccid` int(4) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `sort_order` smallint(2) unsigned NOT NULL default '0',
  `ifshow` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`praid`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_arti_file`
--

DROP TABLE IF EXISTS `oun_pravail_arti_file`;
CREATE TABLE `oun_pravail_arti_file` (
  `fileid` mediumint(8) unsigned NOT NULL auto_increment,
  `arid` mediumint(8) unsigned NOT NULL default '0',
  `user_id` int(6) unsigned NOT NULL default '0',
  `filename` varchar(100) NOT NULL default '',
  `thumb_url` varchar(100) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `domain_id` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`fileid`),
  KEY `arid` (`arid`),
  KEY `domain_id` (`domain_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_article`
--

DROP TABLE IF EXISTS `oun_pravail_article`;
CREATE TABLE `oun_pravail_article` (
  `arid` mediumint(8) unsigned NOT NULL default '0',
  `user_id` smallint(5) NOT NULL default '0',
  `praid` int(5) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `descs` text,
  `states` tinyint(1) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`arid`),
  KEY `domain_id` (`domain_id`),
  KEY `praid` (`praid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_artitxt`
--

DROP TABLE IF EXISTS `oun_pravail_artitxt`;
CREATE TABLE `oun_pravail_artitxt` (
  `arid` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` smallint(5) NOT NULL default '0',
  `praid` int(5) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `dateadd` int(11) NOT NULL default '0',
  `comms` int(5) NOT NULL default '0',
  `min_thumb` varchar(100) NOT NULL default '',
  `arti_thumb` varchar(100) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`arid`),
  KEY `states` (`states`),
  KEY `domain_id` (`domain_id`),
  KEY `praid` (`praid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_prattrival`
--

DROP TABLE IF EXISTS `oun_pravail_prattrival`;
CREATE TABLE `oun_pravail_prattrival` (
  `pavid` mediumint(8) unsigned NOT NULL auto_increment,
  `paid` smallint(5) unsigned NOT NULL default '0',
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `pavals` varchar(100) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pavid`),
  KEY `domain_id` (`domain_id`),
  KEY `prid` (`prid`,`paid`)
) TYPE=MyISAM AUTO_INCREMENT=15;

--
-- Table structure for table `oun_pravail_product`
--

DROP TABLE IF EXISTS `oun_pravail_product`;
CREATE TABLE `oun_pravail_product` (
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `descs` text,
  `file_exp` varchar(150) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_product_comms`
--

DROP TABLE IF EXISTS `oun_pravail_product_comms`;
CREATE TABLE `oun_pravail_product_comms` (
  `prcid` mediumint(8) unsigned NOT NULL auto_increment,
  `praid` int(6) unsigned NOT NULL default '0',
  `prid` mediumint(8) NOT NULL default '0',
  `ip` varchar(16) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `descs` text,
  `dateadd` int(11) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prcid`),
  KEY `domain_id` (`domain_id`),
  KEY `praid` (`praid`),
  KEY `prid` (`prid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_product_file`
--

DROP TABLE IF EXISTS `oun_pravail_product_file`;
CREATE TABLE `oun_pravail_product_file` (
  `fileid` mediumint(8) unsigned NOT NULL auto_increment,
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `user_id` int(6) unsigned NOT NULL default '0',
  `filename` varchar(100) NOT NULL default '',
  `thumb_url` varchar(100) NOT NULL default '',
  `shop_thumb` varchar(100) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `domain_id` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`fileid`),
  KEY `workid` (`prid`),
  KEY `domain_id` (`domain_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_productcat`
--

DROP TABLE IF EXISTS `oun_pravail_productcat`;
CREATE TABLE `oun_pravail_productcat` (
  `prapcid` int(6) unsigned NOT NULL auto_increment,
  `praid` int(5) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `ifshow` tinyint(1) NOT NULL default '1',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`prapcid`),
  KEY `domain_id` (`domain_id`),
  KEY `praid` (`praid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_pravail_producttxt`
--

DROP TABLE IF EXISTS `oun_pravail_producttxt`;
CREATE TABLE `oun_pravail_producttxt` (
  `prid` mediumint(8) unsigned NOT NULL auto_increment,
  `main_prid` int(8) unsigned NOT NULL default '0',
  `prapcid` smallint(5) NOT NULL default '0',
  `praid` int(5) unsigned NOT NULL default '0',
  `pacid` smallint(5) unsigned NOT NULL default '0',
  `prbid` smallint(5) unsigned NOT NULL default '0',
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `shop_sn` varchar(60) NOT NULL default '',
  `shop_price` decimal(10,2) unsigned NOT NULL default '0.00',
  `up_date` int(10) NOT NULL default '0',
  `shop_number` smallint(5) unsigned NOT NULL default '0',
  `min_thumb` varchar(100) NOT NULL default '',
  `shop_thumb` varchar(100) NOT NULL default '',
  `top` tinyint(1) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `comms` int(5) NOT NULL default '0',
  `hots` int(5) unsigned NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prid`),
  KEY `states` (`states`),
  KEY `domain_id` (`domain_id`),
  KEY `pacid` (`pacid`),
  KEY `prbid` (`prbid`),
  KEY `shop_price` (`shop_price`),
  KEY `user_id` (`user_id`),
  KEY `hots` (`hots`),
  KEY `praid` (`praid`),
  KEY `prapcid` (`prapcid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_price_history`
--

DROP TABLE IF EXISTS `oun_price_history`;
CREATE TABLE `oun_price_history` (
  `prhid` mediumint(8) unsigned NOT NULL auto_increment,
  `praid` int(5) unsigned NOT NULL default '0',
  `prid` int(8) unsigned NOT NULL default '0',
  `shop_price` decimal(10,2) unsigned NOT NULL default '0.00',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prhid`),
  KEY `prid` (`prid`),
  KEY `praid` (`praid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_probrand`
--

DROP TABLE IF EXISTS `oun_probrand`;
CREATE TABLE `oun_probrand` (
  `prbid` smallint(5) unsigned NOT NULL auto_increment,
  `brand_name` varchar(60) NOT NULL default '',
  `brand_logo` varchar(80) NOT NULL default '',
  `brand_desc` text NOT NULL,
  `site_url` varchar(255) NOT NULL default '',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `is_show` tinyint(1) unsigned NOT NULL default '1',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`prbid`),
  KEY `is_show` (`is_show`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_probrand_procat`
--

DROP TABLE IF EXISTS `oun_probrand_procat`;
CREATE TABLE `oun_probrand_procat` (
  `ppid` int(6) unsigned NOT NULL auto_increment,
  `prbid` smallint(5) unsigned NOT NULL default '0',
  `pcid` int(6) unsigned NOT NULL default '0',
  `counts` int(6) unsigned NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`ppid`),
  KEY `prbid` (`prbid`,`pcid`,`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_product`
--

DROP TABLE IF EXISTS `oun_product`;
CREATE TABLE `oun_product` (
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `descs` text,
  `cltion` text NOT NULL,
  `cltion_product` text NOT NULL,
  `cltion_topic` text NOT NULL,
  `file_exp` varchar(150) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_product_comms`
--

DROP TABLE IF EXISTS `oun_product_comms`;
CREATE TABLE `oun_product_comms` (
  `prcid` mediumint(8) unsigned NOT NULL auto_increment,
  `prid` mediumint(8) NOT NULL default '0',
  `name` varchar(10) NOT NULL default '',
  `ip` varchar(16) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `descs` text,
  `dateadd` int(11) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prcid`),
  KEY `domain_id` (`domain_id`),
  KEY `user_id` (`user_id`),
  KEY `prid` (`prid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_product_file`
--

DROP TABLE IF EXISTS `oun_product_file`;
CREATE TABLE `oun_product_file` (
  `fileid` mediumint(8) unsigned NOT NULL auto_increment,
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `user_id` int(6) unsigned NOT NULL default '0',
  `filename` varchar(100) NOT NULL default '',
  `thumb_url` varchar(100) NOT NULL default '',
  `shop_thumb` varchar(100) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `domain_id` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`fileid`),
  KEY `workid` (`prid`),
  KEY `domain_id` (`domain_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_productcat`
--

DROP TABLE IF EXISTS `oun_productcat`;
CREATE TABLE `oun_productcat` (
  `pcid` int(6) unsigned NOT NULL auto_increment,
  `fid` int(5) NOT NULL default '0',
  `next_node` varchar(80) NOT NULL default '',
  `name` varchar(60) NOT NULL default '',
  `descs` varchar(240) NOT NULL default '',
  `keywords` varchar(250) NOT NULL default '',
  `ifshow` tinyint(1) NOT NULL default '1',
  `ifnav` tinyint(1) NOT NULL default '0',
  `ifhot` tinyint(1) NOT NULL default '0',
  `pro_interval` varchar(250) NOT NULL default '',
  `acids` varchar(60) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pcid`),
  KEY `fid` (`fid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_producttxt`
--

DROP TABLE IF EXISTS `oun_producttxt`;
CREATE TABLE `oun_producttxt` (
  `prid` mediumint(8) unsigned NOT NULL auto_increment,
  `pcid` smallint(5) NOT NULL default '0',
  `pacid` smallint(5) unsigned NOT NULL default '0',
  `prbid` smallint(5) unsigned NOT NULL default '0',
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `edit_comm` varchar(255) NOT NULL default '',
  `praids` varchar(100) NOT NULL default '',
  `shop_sn` varchar(60) NOT NULL default '',
  `shop_price` decimal(10,2) unsigned NOT NULL default '0.00',
  `up_date` int(10) NOT NULL default '0',
  `shop_number` smallint(5) unsigned NOT NULL default '0',
  `s_discount` double(10,2) NOT NULL default '0.00',
  `s_dis_exp` varchar(50) NOT NULL default '',
  `min_thumb` varchar(100) NOT NULL default '',
  `shop_thumb` varchar(100) NOT NULL default '',
  `top` tinyint(1) NOT NULL default '0',
  `special` tinyint(1) NOT NULL default '0',
  `colors` varchar(7) NOT NULL default '',
  `dateadd` int(11) NOT NULL default '0',
  `comms` int(5) NOT NULL default '0',
  `hots` int(5) unsigned NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prid`),
  KEY `states` (`states`),
  KEY `domain_id` (`domain_id`),
  KEY `pacid` (`pacid`),
  KEY `prbid` (`prbid`),
  KEY `shop_price` (`shop_price`),
  KEY `user_id` (`user_id`),
  KEY `hots` (`hots`),
  KEY `special` (`special`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_prtopra`
--

DROP TABLE IF EXISTS `oun_prtopra`;
CREATE TABLE `oun_prtopra` (
  `ptpid` mediumint(8) unsigned NOT NULL auto_increment,
  `praid` int(5) unsigned NOT NULL default '0',
  `prid` int(8) unsigned NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`ptpid`),
  KEY `prid` (`prid`),
  KEY `praid` (`praid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_qq`
--

DROP TABLE IF EXISTS `oun_qq`;
CREATE TABLE `oun_qq` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `qq` int(8) unsigned NOT NULL default '0',
  `qq_name` varchar(40) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_sernet`
--

DROP TABLE IF EXISTS `oun_sernet`;
CREATE TABLE `oun_sernet` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `py` varchar(20) NOT NULL default '',
  `name` varchar(16) NOT NULL default '',
  `name_desc` text NOT NULL,
  `url` varchar(150) NOT NULL default '',
  `stats` tinyint(1) NOT NULL default '1',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=101;

--
-- Table structure for table `oun_sessions`
--

DROP TABLE IF EXISTS `oun_sessions`;
CREATE TABLE `oun_sessions` (
  `sesskey` char(32) binary NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  `adminid` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(15) NOT NULL default '',
  `data` char(255) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sesskey`),
  KEY `expiry` (`expiry`),
  KEY `domain_id` (`domain_id`),
  KEY `userid` (`userid`)
) TYPE=HEAP;

--
-- Table structure for table `oun_sessions_data`
--

DROP TABLE IF EXISTS `oun_sessions_data`;
CREATE TABLE `oun_sessions_data` (
  `sesskey` varchar(32) binary NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`sesskey`),
  KEY `expiry` (`expiry`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_sesspro`
--

DROP TABLE IF EXISTS `oun_sesspro`;
CREATE TABLE `oun_sesspro` (
  `sesskey` char(32) binary NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  `prid` mediumint(8) unsigned NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`expiry`),
  KEY `domain_id` (`domain_id`),
  KEY `userid` (`userid`),
  KEY `prid` (`prid`),
  KEY `sesskey` (`sesskey`)
) TYPE=HEAP;

--
-- Table structure for table `oun_support`
--

DROP TABLE IF EXISTS `oun_support`;
CREATE TABLE `oun_support` (
  `spid` mediumint(8) unsigned NOT NULL auto_increment,
  `users_id` mediumint(8) NOT NULL default '0',
  `comms` int(5) unsigned NOT NULL default '0',
  `ip` varchar(16) NOT NULL default '',
  `name` varchar(10) NOT NULL default '',
  `tel` varchar(30) NOT NULL default '',
  `pos` varchar(7) NOT NULL default '',
  `addrs` varchar(80) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `supports` text,
  `dateadd` int(11) NOT NULL default '0',
  `orderdate` int(11) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`spid`),
  KEY `domain_id` (`domain_id`),
  KEY `orderdate` (`orderdate`),
  KEY `users_id` (`users_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_support_re`
--

DROP TABLE IF EXISTS `oun_support_re`;
CREATE TABLE `oun_support_re` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `spid` mediumint(8) NOT NULL default '0',
  `users_id` mediumint(8) unsigned NOT NULL default '0',
  `ip` varchar(16) NOT NULL default '',
  `supports` text,
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `spid` (`spid`),
  KEY `domain_id` (`domain_id`),
  KEY `dateadd` (`dateadd`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_sysconfig`
--

DROP TABLE IF EXISTS `oun_sysconfig`;
CREATE TABLE `oun_sysconfig` (
  `scid` int(6) unsigned NOT NULL auto_increment,
  `user_id` smallint(5) unsigned NOT NULL default '0',
  `user_name` varchar(30) NOT NULL default '',
  `main_domin` varchar(80) NOT NULL default '',
  `sets` text NOT NULL,
  `home` text NOT NULL,
  `header_title` varchar(80) NOT NULL default '',
  `descs` text NOT NULL,
  `notices` text NOT NULL,
  `logo` varchar(100) NOT NULL default '',
  `shop_logo` varchar(100) NOT NULL default '',
  `watermark` varchar(100) NOT NULL default '',
  `ccid` int(2) unsigned NOT NULL default '1',
  `inducatid` int(5) unsigned NOT NULL default '0',
  `template` varchar(20) NOT NULL default '',
  `user_template` tinyint(1) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `hots` int(6) unsigned NOT NULL default '0',
  `rewrite` tinyint(1) NOT NULL default '0',
  `pre_scid` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`scid`),
  KEY `user_id` (`user_id`),
  KEY `country` (`ccid`),
  KEY `main_domin` (`main_domin`,`user_name`),
  KEY `states` (`states`),
  KEY `hots` (`hots`),
  KEY `rewrite` (`rewrite`),
  KEY `inducatid` (`inducatid`)
) TYPE=MyISAM AUTO_INCREMENT=326;

--
-- Table structure for table `oun_sysconfigfast`
--

DROP TABLE IF EXISTS `oun_sysconfigfast`;
CREATE TABLE `oun_sysconfigfast` (
  `scid` int(6) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) NOT NULL default '',
  `main_domin` varchar(80) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`scid`),
  KEY `main_domin` (`main_domin`,`user_name`),
  KEY `states` (`states`),
  KEY `user_name` (`user_name`)
) TYPE=MyISAM AUTO_INCREMENT=326;

--
-- Table structure for table `oun_sysnotice`
--

DROP TABLE IF EXISTS `oun_sysnotice`;
CREATE TABLE `oun_sysnotice` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `notices` text NOT NULL,
  `dateadd` timestamp NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_syssmtp`
--

DROP TABLE IF EXISTS `oun_syssmtp`;
CREATE TABLE `oun_syssmtp` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `smtpusermail` varchar(100) NOT NULL default '',
  `smtppass` varchar(100) NOT NULL default '',
  `smtpserver` varchar(100) NOT NULL default '',
  `smtpport` int(2) NOT NULL default '0',
  `domain_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_systhemes`
--

DROP TABLE IF EXISTS `oun_systhemes`;
CREATE TABLE `oun_systhemes` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `navid` mediumint(8) unsigned NOT NULL default '0',
  `systhemes` text NOT NULL,
  `dateadd` int(11) unsigned NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`),
  KEY `navid` (`navid`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_tj`
--

DROP TABLE IF EXISTS `oun_tj`;
CREATE TABLE `oun_tj` (
  `cgid` mediumint(8) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `ext` varchar(250) NOT NULL default '',
  `ex1` varchar(200) NOT NULL default '',
  `ex2` varchar(200) NOT NULL default '',
  `tjcatid` int(5) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `colors` varchar(8) NOT NULL default '',
  `orders` int(5) unsigned NOT NULL default '0',
  `img` varchar(200) NOT NULL default '',
  `domain_id` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cgid`),
  KEY `domain_id` (`domain_id`),
  KEY `orders` (`orders`),
  KEY `tjcatid` (`tjcatid`)
) TYPE=MyISAM AUTO_INCREMENT=12;

--
-- Table structure for table `oun_tjcat`
--

DROP TABLE IF EXISTS `oun_tjcat`;
CREATE TABLE `oun_tjcat` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `imgwidth` int(5) NOT NULL default '0',
  `imgheight` int(5) NOT NULL default '0',
  `limits` int(3) NOT NULL default '0',
  `showtype` tinyint(1) NOT NULL default '0',
  `orders` int(2) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=31;

--
-- Table structure for table `oun_uaddrs`
--

DROP TABLE IF EXISTS `oun_uaddrs`;
CREATE TABLE `oun_uaddrs` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `users_id` mediumint(8) unsigned NOT NULL default '0',
  `ccid` int(6) unsigned NOT NULL default '0',
  `addrs` varchar(250) NOT NULL default '',
  `zip` varchar(6) NOT NULL default '',
  `name` varchar(20) NOT NULL default '',
  `tel` varchar(36) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` smallint(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `users_id` (`users_id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_udetail`
--

DROP TABLE IF EXISTS `oun_udetail`;
CREATE TABLE `oun_udetail` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  `bankname` varchar(50) NOT NULL default '',
  `remmoney` decimal(10,2) NOT NULL default '0.00',
  `payname` varchar(20) NOT NULL default '',
  `paynums` varchar(150) NOT NULL default '',
  `dateadd` int(11) NOT NULL default '0',
  `checked` tinyint(1) NOT NULL default '0',
  `checkdesc` varchar(250) NOT NULL default '',
  `domain_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `users_id` (`users_id`),
  KEY `domain_id` (`domain_id`),
  KEY `type` (`type`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_ufv`
--

DROP TABLE IF EXISTS `oun_ufv`;
CREATE TABLE `oun_ufv` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL default '0',
  `prid` tinyint(1) NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `users_id` (`users_id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_urlrecord`
--

DROP TABLE IF EXISTS `oun_urlrecord`;
CREATE TABLE `oun_urlrecord` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `mainurl` varchar(80) NOT NULL default '',
  `adddate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_userfindpass`
--

DROP TABLE IF EXISTS `oun_userfindpass`;
CREATE TABLE `oun_userfindpass` (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `findpass` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_users`
--

DROP TABLE IF EXISTS `oun_users`;
CREATE TABLE `oun_users` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `utid` int(6) unsigned NOT NULL default '0',
  `email` varchar(160) NOT NULL default '',
  `user_name` varchar(20) NOT NULL default '',
  `money` decimal(10,2) NOT NULL default '0.00',
  `avatar` mediumint(8) unsigned NOT NULL default '0',
  `password` varchar(32) NOT NULL default '',
  `sex` tinyint(1) unsigned NOT NULL default '0',
  `birthday` date NOT NULL default '0000-00-00',
  `reg_time` int(10) unsigned NOT NULL default '0',
  `last_login` int(11) unsigned NOT NULL default '0',
  `last_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_ip` varchar(15) NOT NULL default '',
  `visit_count` smallint(5) unsigned NOT NULL default '0',
  `qq` varchar(20) NOT NULL default '',
  `mobile_phone` varchar(20) NOT NULL default '',
  `addrs` varchar(200) NOT NULL default '',
  `userhttp` varchar(250) NOT NULL default '',
  `usertag` varchar(250) NOT NULL default '',
  `states` tinyint(1) NOT NULL default '0',
  `ifmanger` tinyint(1) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM AUTO_INCREMENT=2;

--
-- Table structure for table `oun_users_comms`
--

DROP TABLE IF EXISTS `oun_users_comms`;
CREATE TABLE `oun_users_comms` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `users_id` mediumint(8) unsigned NOT NULL default '0',
  `coms_type` tinyint(1) unsigned NOT NULL default '0',
  `arid` int(6) unsigned NOT NULL default '0',
  `dateadd` int(11) NOT NULL default '0',
  `domain_id` smallint(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `users_id` (`users_id`,`coms_type`,`arid`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_users_job`
--

DROP TABLE IF EXISTS `oun_users_job`;
CREATE TABLE `oun_users_job` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `users_id` int(6) unsigned NOT NULL default '0',
  `xingming` varchar(20) NOT NULL default '',
  `sex` varchar(4) NOT NULL default '',
  `mingzu` varchar(20) NOT NULL default '',
  `hunyingzk` varchar(20) NOT NULL default '',
  `shengri` varchar(30) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `tel` varchar(80) NOT NULL default '',
  `idc` varchar(20) NOT NULL default '',
  `jingjitel` varchar(80) NOT NULL default '',
  `addres` varchar(120) NOT NULL default '',
  `yingpingzw` varchar(120) NOT NULL default '',
  `arid` mediumint(8) unsigned NOT NULL default '0',
  `jobstate` varchar(20) NOT NULL default '',
  `qiwangxz` varchar(20) NOT NULL default '',
  `daogangtime` varchar(20) NOT NULL default '',
  `bieyexx` text NOT NULL,
  `gongzuojl` text NOT NULL,
  `descs` text NOT NULL,
  `dateadd` int(11) unsigned NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `addip` varchar(60) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`),
  KEY `arid` (`arid`),
  KEY `dateadd` (`dateadd`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_userstype`
--

DROP TABLE IF EXISTS `oun_userstype`;
CREATE TABLE `oun_userstype` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(160) NOT NULL default '',
  `orders` int(6) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `domain_id` (`domain_id`),
  KEY `orders` (`orders`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_usersverify`
--

DROP TABLE IF EXISTS `oun_usersverify`;
CREATE TABLE `oun_usersverify` (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `email` varchar(160) NOT NULL default '',
  `estats` tinyint(1) NOT NULL default '0',
  `edate` datetime NOT NULL default '0000-00-00 00:00:00',
  `tel` varchar(20) NOT NULL default '',
  `tstats` tinyint(1) NOT NULL default '0',
  `tdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  KEY `domain_id` (`domain_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_vote_group`
--

DROP TABLE IF EXISTS `oun_vote_group`;
CREATE TABLE `oun_vote_group` (
  `vgid` int(8) unsigned NOT NULL auto_increment,
  `vtid` int(8) unsigned NOT NULL default '0',
  `vg_name` varchar(250) NOT NULL default '',
  `vg_desc` text NOT NULL,
  `is_show` tinyint(1) NOT NULL default '1',
  `vg_nums` int(5) NOT NULL default '0',
  `orders` int(5) NOT NULL default '0',
  `thumb_url_w` int(4) unsigned NOT NULL default '0',
  `thumb_url_h` int(4) NOT NULL default '0',
  `thumb_s_url_w` int(4) NOT NULL default '0',
  `thumb_s_url_h` int(4) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`vgid`),
  KEY `domain_id` (`domain_id`),
  KEY `orders` (`orders`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_vote_ip`
--

DROP TABLE IF EXISTS `oun_vote_ip`;
CREATE TABLE `oun_vote_ip` (
  `vipid` int(8) unsigned NOT NULL auto_increment,
  `vtid` int(8) unsigned NOT NULL default '0',
  `ip` varchar(40) NOT NULL default '',
  `users_id` mediumint(8) unsigned NOT NULL default '0',
  `add_time` int(8) unsigned NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`vipid`),
  KEY `domain_id` (`domain_id`),
  KEY `users_id` (`users_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_vote_item`
--

DROP TABLE IF EXISTS `oun_vote_item`;
CREATE TABLE `oun_vote_item` (
  `viid` int(8) unsigned NOT NULL auto_increment,
  `vtid` int(8) unsigned NOT NULL default '0',
  `vgid` int(8) unsigned NOT NULL default '0',
  `vi_name` varchar(250) NOT NULL default '',
  `vi_type` tinyint(1) unsigned NOT NULL default '0',
  `is_show` tinyint(1) NOT NULL default '1',
  `states` tinyint(1) unsigned NOT NULL default '0',
  `orders` int(3) NOT NULL default '0',
  `vi_nums` int(1) unsigned NOT NULL default '5',
  `thumb_url` varchar(80) NOT NULL default '',
  `thumb_s_url` varchar(80) NOT NULL default '',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`viid`),
  KEY `is_show` (`states`),
  KEY `domain_id` (`domain_id`),
  KEY `vtid` (`vtid`),
  KEY `vgid` (`vgid`),
  KEY `orders` (`orders`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_vote_poll`
--

DROP TABLE IF EXISTS `oun_vote_poll`;
CREATE TABLE `oun_vote_poll` (
  `vipid` int(8) unsigned NOT NULL auto_increment,
  `vtid` int(8) unsigned NOT NULL default '0',
  `viid` int(8) NOT NULL default '0',
  `descs` varchar(250) NOT NULL default '',
  `ip` varchar(40) NOT NULL default '',
  `computer` varchar(80) NOT NULL default '',
  `users_id` mediumint(8) unsigned NOT NULL default '0',
  `user_name` varchar(20) NOT NULL default '',
  `add_time` int(8) unsigned NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`vipid`),
  KEY `domain_id` (`domain_id`),
  KEY `viid` (`viid`),
  KEY `ip` (`ip`,`computer`),
  KEY `users_id` (`users_id`)
) TYPE=MyISAM;

--
-- Table structure for table `oun_vote_title`
--

DROP TABLE IF EXISTS `oun_vote_title`;
CREATE TABLE `oun_vote_title` (
  `vtid` int(8) unsigned NOT NULL auto_increment,
  `vt_name` varchar(60) NOT NULL default '',
  `vt_desc` text NOT NULL,
  `add_time` int(11) unsigned NOT NULL default '0',
  `is_show` tinyint(1) unsigned NOT NULL default '1',
  `top` int(3) NOT NULL default '0',
  `states` tinyint(1) NOT NULL default '0',
  `xianz` tinyint(1) NOT NULL default '0',
  `xianz_num` int(6) unsigned NOT NULL default '0',
  `showtype` tinyint(1) NOT NULL default '0',
  `vt_nums` int(1) unsigned NOT NULL default '0',
  `arid` mediumint(8) NOT NULL default '0',
  `domain_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`vtid`),
  KEY `is_show` (`is_show`),
  KEY `domain_id` (`domain_id`),
  KEY `top` (`top`)
) TYPE=MyISAM;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

