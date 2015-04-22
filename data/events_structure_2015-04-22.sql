# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.6.22)
# Database: events
# Generation Time: 2015-04-22 21:08:39 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table account
# ------------------------------------------------------------

DROP TABLE IF EXISTS `account`;

CREATE TABLE `account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `streetaddress1` varchar(255) DEFAULT NULL,
  `streetaddress2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `accountstatus` varchar(100) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `sponsor_id` int(11) NOT NULL DEFAULT '0',
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table admissioncharge
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admissioncharge`;

CREATE TABLE `admissioncharge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admissioninfogroup_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table admissioninfo
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admissioninfo`;

CREATE TABLE `admissioninfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `obligation` varchar(100) DEFAULT NULL,
  `contactname` varchar(100) DEFAULT NULL,
  `contactphone` varchar(50) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `contacturl` longtext,
  `status` varchar(255) DEFAULT NULL,
  `additionalinfo` longtext,
  `deadline` datetime DEFAULT NULL,
  `opendate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id_idx` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table attendancerestriction
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendancerestriction`;

CREATE TABLE `attendancerestriction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `attendancerestriction_event_id_idx` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table audience
# ------------------------------------------------------------

DROP TABLE IF EXISTS `audience`;

CREATE TABLE `audience` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table calendar
# ------------------------------------------------------------

DROP TABLE IF EXISTS `calendar`;

CREATE TABLE `calendar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `shortname` varchar(100) DEFAULT NULL,
  `eventreleasepreference` varchar(255) DEFAULT NULL,
  `calendardaterange` int(10) unsigned DEFAULT NULL,
  `formatcalendardata` longtext,
  `uploadedcss` longtext,
  `uploadedxsl` longtext,
  `emaillists` longtext,
  `calendarstatus` varchar(255) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(255) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(255) DEFAULT NULL,
  `externalforms` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `recommendationswithinaccount` tinyint(1) DEFAULT '0',
  `theme` varchar(255) DEFAULT 'base',
  PRIMARY KEY (`id`),
  KEY `account_id_idx` (`account_id`),
  KEY `shortname_idx` (`shortname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table calendar_has_event
# ------------------------------------------------------------

DROP TABLE IF EXISTS `calendar_has_event`;

CREATE TABLE `calendar_has_event` (
  `calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `che_calendar_id_idx` (`calendar_id`),
  KEY `che_event_id_idx` (`event_id`),
  KEY `che_status_idx` (`status`),
  KEY `che_event_calendar_status` (`calendar_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table document
# ------------------------------------------------------------

DROP TABLE IF EXISTS `document`;

CREATE TABLE `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event`;

CREATE TABLE `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT ' ',
  `subtitle` varchar(100) DEFAULT NULL,
  `othereventtype` varchar(255) DEFAULT NULL,
  `description` longtext,
  `shortdescription` varchar(255) DEFAULT NULL,
  `refreshments` varchar(255) DEFAULT NULL,
  `classification` varchar(100) DEFAULT NULL,
  `approvedforcirculation` tinyint(1) DEFAULT NULL,
  `transparency` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `privatecomment` longtext,
  `otherkeywords` varchar(255) DEFAULT NULL,
  `imagetitle` varchar(100) DEFAULT NULL,
  `imageurl` longtext,
  `webpageurl` longtext,
  `listingcontactuid` varchar(255) DEFAULT NULL,
  `listingcontactname` varchar(100) DEFAULT NULL,
  `listingcontactphone` varchar(255) DEFAULT NULL,
  `listingcontactemail` varchar(255) DEFAULT NULL,
  `icalendar` longtext,
  `imagedata` longblob,
  `imagemime` varchar(255) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_title_idx` (`title`),
  KEY `approvedforcirculation` (`approvedforcirculation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event_has_eventtype
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_has_eventtype`;

CREATE TABLE `event_has_eventtype` (
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `eventtype_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `ehe_event_id_idx` (`event_id`),
  KEY `ehe_eventtype_id_idx` (`eventtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event_has_keyword
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_has_keyword`;

CREATE TABLE `event_has_keyword` (
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `keyword_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `ehk_event_id_idx` (`event_id`),
  KEY `ehk_sponsor_id_idx` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event_has_sponsor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_has_sponsor`;

CREATE TABLE `event_has_sponsor` (
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sponsor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `ehs_event_id_idx` (`event_id`),
  KEY `ehs_sponsor_id_idx` (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event_isopento_audience
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_isopento_audience`;

CREATE TABLE `event_isopento_audience` (
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `audience_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `eia_event_id_idx` (`event_id`),
  KEY `eia_audience_id_idx` (`audience_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event_targets_audience
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_targets_audience`;

CREATE TABLE `event_targets_audience` (
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `audience_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `eta_event_id_idx` (`event_id`),
  KEY `eta_audience_id_idx` (`audience_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table eventdatetime
# ------------------------------------------------------------

DROP TABLE IF EXISTS `eventdatetime`;

CREATE TABLE `eventdatetime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `room` varchar(255) DEFAULT NULL,
  `hours` varchar(255) DEFAULT NULL,
  `directions` longtext,
  `additionalpublicinfo` longtext,
  `recurringtype` varchar(255) NOT NULL DEFAULT 'none',
  `recurs_until` datetime DEFAULT NULL,
  `rectypemonth` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `edt_event_id_idx` (`event_id`),
  KEY `edt_location_id_idx` (`location_id`),
  KEY `edt_starttime_idx` (`starttime`),
  KEY `edt_endtime_idx` (`endtime`),
  KEY `edt_starttime_recurringtype` (`starttime`,`recurringtype`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table eventtype
# ------------------------------------------------------------

DROP TABLE IF EXISTS `eventtype`;

CREATE TABLE `eventtype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT ' ',
  `description` varchar(255) DEFAULT NULL,
  `eventtypegroup` varchar(8) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `eventtype_name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table facebook
# ------------------------------------------------------------

DROP TABLE IF EXISTS `facebook`;

CREATE TABLE `facebook` (
  `facebook_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `eventdatetime_id` int(10) unsigned NOT NULL DEFAULT '0',
  `calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page_name` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table facebook_accounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `facebook_accounts`;

CREATE TABLE `facebook_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facebook_account` bigint(20) unsigned NOT NULL DEFAULT '0',
  `access_token` varchar(100) NOT NULL DEFAULT ' ',
  `page_name` varchar(30) DEFAULT NULL,
  `calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_events` tinyint(1) NOT NULL DEFAULT '0',
  `show_like_buttons` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table keyword
# ------------------------------------------------------------

DROP TABLE IF EXISTS `keyword`;

CREATE TABLE `keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table location
# ------------------------------------------------------------

DROP TABLE IF EXISTS `location`;

CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `streetaddress1` varchar(255) DEFAULT NULL,
  `streetaddress2` varchar(255) DEFAULT NULL,
  `room` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `mapurl` longtext,
  `webpageurl` longtext,
  `hours` varchar(255) DEFAULT NULL,
  `directions` longtext,
  `additionalpublicinfo` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1',
  `user_id` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `location_name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ongoingcheck
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ongoingcheck`;

CREATE TABLE `ongoingcheck` (
  `d` date NOT NULL DEFAULT '2013-02-24',
  PRIMARY KEY (`d`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table performer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `performer`;

CREATE TABLE `performer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `performer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `personalname` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `jobtitle` varchar(100) DEFAULT NULL,
  `organizationname` varchar(100) DEFAULT NULL,
  `personalwebpageurl` longtext,
  `organizationwebpageurl` longtext,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table publiccontact
# ------------------------------------------------------------

DROP TABLE IF EXISTS `publiccontact`;

CREATE TABLE `publiccontact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `jobtitle` varchar(100) DEFAULT NULL,
  `organization` varchar(100) DEFAULT NULL,
  `addressline1` varchar(255) DEFAULT NULL,
  `addressline2` varchar(255) DEFAULT NULL,
  `room` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `emailaddress` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `webpageurl` longtext,
  PRIMARY KEY (`id`),
  KEY `event_id_idx` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table recurringdate
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recurringdate`;

CREATE TABLE `recurringdate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recurringdate` date NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `recurrence_id` int(10) unsigned NOT NULL,
  `ongoing` tinyint(1) DEFAULT NULL,
  `unlinked` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `unlinked` (`unlinked`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table relatedevent
# ------------------------------------------------------------

DROP TABLE IF EXISTS `relatedevent`;

CREATE TABLE `relatedevent` (
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `related_event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `relationtype` varchar(100) NOT NULL DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT ' ',
  `standard` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `user_uid` varchar(255) NOT NULL DEFAULT ' ',
  `lastaction` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `data` longtext,
  PRIMARY KEY (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table sponsor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sponsor`;

CREATE TABLE `sponsor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1',
  `sponsortype` varchar(255) DEFAULT NULL,
  `webpageurl` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table subscription
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscription`;

CREATE TABLE `subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `automaticapproval` tinyint(1) NOT NULL DEFAULT '0',
  `timeperiod` date DEFAULT NULL,
  `expirationdate` date DEFAULT NULL,
  `searchcriteria` longtext,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `calendar_id_idx` (`calendar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `uid` varchar(100) NOT NULL DEFAULT ' ',
  `account_id` int(10) unsigned NOT NULL DEFAULT '0',
  `accountstatus` varchar(100) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `calendar_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table user_has_permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_has_permission`;

CREATE TABLE `user_has_permission` (
  `permission_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_uid` varchar(100) NOT NULL DEFAULT ' ',
  `calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `calendar_id` (`calendar_id`),
  KEY `user_uid` (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table webcast
# ------------------------------------------------------------

DROP TABLE IF EXISTS `webcast`;

CREATE TABLE `webcast` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `dateavailable` datetime DEFAULT NULL,
  `playertype` varchar(100) DEFAULT NULL,
  `bandwidth` varchar(255) DEFAULT NULL,
  `additionalinfo` longtext,
  PRIMARY KEY (`id`),
  KEY `event_id_idx` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table webcastlink
# ------------------------------------------------------------

DROP TABLE IF EXISTS `webcastlink`;

CREATE TABLE `webcastlink` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `webcast_id` int(10) unsigned NOT NULL DEFAULT '0',
  `url` longtext,
  `sequencenumber` int(10) unsigned DEFAULT NULL,
  `related` varchar(1) DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
