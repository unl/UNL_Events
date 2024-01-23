-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 19, 2023 at 04:38 PM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `events`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `website` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `account` (`id`,`name`,`streetaddress1`,`streetaddress2`,`city`,`state`,`zip`,`phone`,`fax`,`email`,`accountstatus`,`datecreated`,`datelastupdated`,`sponsor_id`,`website`) VALUES
(1, 'UNL ALL', '', '', 'Lincoln', 'NE', '', '', '', '', 'accountstatus', '2000-02-18 02:00:00', '2000-02-18 02:00:00', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `admissioncharge`
--

DROP TABLE IF EXISTS `admissioncharge`;
CREATE TABLE `admissioncharge` (
  `id` int(10) UNSIGNED NOT NULL,
  `admissioninfogroup_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `price` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admissioninfo`
--

DROP TABLE IF EXISTS `admissioninfo`;
CREATE TABLE `admissioninfo` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `obligation` varchar(100) DEFAULT NULL,
  `contactname` varchar(100) DEFAULT NULL,
  `contactphone` varchar(50) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `contacturl` longtext,
  `status` varchar(255) DEFAULT NULL,
  `additionalinfo` longtext,
  `deadline` datetime DEFAULT NULL,
  `opendate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attendancerestriction`
--

DROP TABLE IF EXISTS `attendancerestriction`;
CREATE TABLE `attendancerestriction` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `audience`
--

DROP TABLE IF EXISTS `audience`;
CREATE TABLE `audience` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audience`
--

INSERT INTO `audience` (`id`, `name`, `standard`) VALUES
(1, 'Undergraduate Students', 1),
(2, 'Graduate Students', 1),
(3, 'Prospective Students', 1),
(4, 'Staff', 1),
(5, 'Faculty', 1),
(6, 'Alumni', 1),
(7, 'Public', 1),
(8, 'Postdoctoral', 1);

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
CREATE TABLE `calendar` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `shortname` varchar(100) DEFAULT NULL,
  `eventreleasepreference` varchar(255) DEFAULT NULL,
  `calendardaterange` int(10) UNSIGNED DEFAULT NULL,
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
  `defaulttimezone` varchar(30) NOT NULL DEFAULT 'America/Chicago'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `calendar` (`id`, `account_id`, `name`,`shortname`, `eventreleasepreference`, `calendardaterange`,`formatcalendardata`,`uploadedcss`,`uploadedxsl`,`emaillists`,`calendarstatus`,`datecreated`,`uidcreated`,`datelastupdated`,`uidlastupdated`,`externalforms`,`website`,`recommendationswithinaccount`,`theme`,`defaulttimezone`) VALUES
(1, 1, 'University of Nebraska-Lincoln', 'unl', '', 0, '', '', '', '', '', '2000-02-18 02:00:00', 'tneumann9', '2000-02-18 02:00:00', 'tneumann9', '', null, 0, 'base', 'America/Chicago');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_has_event`
--

DROP TABLE IF EXISTS `calendar_has_event`;
CREATE TABLE `calendar_has_event` (
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('posted','pending','archived') DEFAULT NULL,
  `source` enum('checked consider event','create event form','recommended','search','subscription','create event api','create event api v2') DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `id` int(10) UNSIGNED NOT NULL,
  `featured` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
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
  `listingcontacturl` longtext,
  `listingcontacttype` enum('person','organization') DEFAULT NULL,
  `icalendar` longtext,
  `imagedata` longblob,
  `imagemime` varchar(255) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `promoted` varchar(255) DEFAULT NULL,
  `canceled` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventdatetime`
--

DROP TABLE IF EXISTS `eventdatetime`;
CREATE TABLE `eventdatetime` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `webcast_id` int(10) UNSIGNED DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `timemode` enum('REGULAR', 'STARTTIMEONLY', 'ENDTIMEONLY', 'ALLDAY', 'TBD') DEFAULT 'REGULAR' NOT NULL,
  `timezone` varchar(30) NOT NULL DEFAULT 'America/Chicago',
  `room` varchar(255) DEFAULT NULL,
  `hours` varchar(255) DEFAULT NULL,
  `directions` longtext,
  `additionalpublicinfo` longtext,
  `location_additionalpublicinfo` longtext,
  `webcast_additionalpublicinfo` longtext,
  `recurringtype` varchar(255) NOT NULL DEFAULT 'none',
  `recurs_until` datetime DEFAULT NULL,
  `rectypemonth` varchar(255) DEFAULT NULL,
  `canceled` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventtype`
--

DROP TABLE IF EXISTS `eventtype`;
CREATE TABLE `eventtype` (
  `id` int(10) UNSIGNED NOT NULL,
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT ' ',
  `description` varchar(255) DEFAULT NULL,
  `eventtypegroup` varchar(8) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eventtype`
--

INSERT INTO `eventtype` (`id`, `calendar_id`, `name`, `description`, `eventtypegroup`, `standard`) VALUES
(1, 0, 'Career Fair', 'Career Fair', NULL, 1),
(2, 0, 'Colloquium', 'Colloquium', NULL, 1),
(3, 0, 'Conference/Symposium', 'Conference/Symposium', NULL, 1),
(4, 0, 'Course', 'Course', NULL, 1),
(5, 0, 'Deadline', 'Deadline', NULL, 1),
(6, 0, 'Debate/Panel Discussion', 'Debate/Panel Discussion', NULL, 1),
(7, 0, 'Exhibit - Artifacts', 'Exhibit - Artifacts', NULL, 1),
(8, 0, 'Exhibit - Multimedia', 'Exhibit - Multimedia', NULL, 1),
(9, 0, 'Exhibit - Painting', 'Exhibit - Painting', NULL, 1),
(10, 0, 'Exhibit - Photography', 'Exhibit - Photography', NULL, 1),
(11, 0, 'Exhibit - Sculpture', 'Exhibit - Sculpture', NULL, 1),
(12, 0, 'Film - Animated', 'Film - Animated', NULL, 1),
(13, 0, 'Film - Documentary', 'Film - Documentary', NULL, 1),
(14, 0, 'Film - Feature', 'Film - Feature', NULL, 1),
(15, 0, 'Film - Series', 'Film - Series', NULL, 1),
(16, 0, 'Film - Short', 'Film - Short', NULL, 1),
(18, 0, 'Information Session', 'Information Session', NULL, 1),
(19, 0, 'Lecture', 'Lecture', NULL, 1),
(20, 0, 'Meeting', 'Meeting', NULL, 1),
(21, 0, 'Memorial', 'Memorial', NULL, 1),
(22, 0, 'Other', 'Other', NULL, 1),
(23, 0, 'Performing Arts - Dance', 'Performing Arts - Dance', NULL, 1),
(24, 0, 'Performing Arts - Music', 'Performing Arts - Music', NULL, 1),
(25, 0, 'Performing Arts - Other', 'Performing Arts - Other', NULL, 1),
(26, 0, 'Performing Arts - Theater', 'Performing Arts - Theater', NULL, 1),
(27, 0, 'Presentation', 'Presentation', NULL, 1),
(28, 0, 'Reading - Fiction/poetry', 'Reading - Fiction/poetry', NULL, 1),
(29, 0, 'Reading - Nonfiction', 'Reading - Nonfiction', NULL, 1),
(30, 0, 'Reception', 'Reception', NULL, 1),
(31, 0, 'Sale', 'Sale', NULL, 1),
(32, 0, 'Seminar', 'Seminar', NULL, 1),
(33, 0, 'Social Event', 'Social Event', NULL, 1),
(34, 0, 'Special Event', 'Special Event', NULL, 1),
(35, 0, 'Sport - Club', 'Sport - Club', NULL, 1),
(36, 0, 'Sport - Intercollegiate - Baseball/Softball', 'Sport - Intercollegiate - Baseball/Softball', NULL, 1),
(37, 0, 'Sport - Intercollegiate - Basketball', 'Sport - Intercollegiate - Basketball', NULL, 1),
(38, 0, 'Sport - Intercollegiate - Crew', 'Sport - Intercollegiate - Crew', NULL, 1),
(39, 0, 'Sport - Intercollegiate - Cross Country', 'Sport - Intercollegiate - Cross Country', NULL, 1),
(40, 0, 'Sport - Intercollegiate - Football', 'Sport - Intercollegiate - Football', NULL, 1),
(41, 0, 'Sport - Intercollegiate - Golf', 'Sport - Intercollegiate - Golf', NULL, 1),
(42, 0, 'Sport - Intercollegiate - Gymnastics', 'Sport - Intercollegiate - Gymnastics', NULL, 1),
(43, 0, 'Sport - Intercollegiate - Rugby', 'Sport - Intercollegiate - Rugby', NULL, 1),
(44, 0, 'Sport - Intercollegiate - Soccer', 'Sport - Intercollegiate - Soccer', NULL, 1),
(45, 0, 'Sport - Intercollegiate - Swimming & Diving', 'Sport - Intercollegiate - Swimming & Diving', NULL, 1),
(46, 0, 'Sport - Intercollegiate - Tennis', 'Sport - Intercollegiate - Tennis', NULL, 1),
(47, 0, 'Sport - Intercollegiate - Track & Field', 'Sport - Intercollegiate - Track & Field', NULL, 1),
(48, 0, 'Sport - Intercollegiate - Volleyball', 'Sport - Intercollegiate - Volleyball', NULL, 1),
(49, 0, 'Sport - Intramural', 'Sport - Intramural', NULL, 1),
(50, 0, 'Sport - Recreational', 'Sport - Recreational', NULL, 1),
(51, 0, 'Tour/Open House', 'Tour/Open House', NULL, 1),
(52, 0, 'Workshop', 'Workshop', NULL, 1),
(53, 0, 'Exhibition', NULL, NULL, 1),
(54, 0, 'Planetarium Show', NULL, NULL, 1),
(55, 0, 'Performing Arts - Musical Theatre', 'Performing Arts - Musical Theatre', NULL, 1),
(56, 0, 'Performing Arts - Opera', 'Performing Arts - Opera', NULL, 1),
(57, 0, 'Sport - Intercollegiate - Rifle', 'Sport - Intercollegiate - Rifle', NULL, 1),
(58, 0, 'Sport - Intercollegiate - Wrestling', 'Sport - Intercollegiate - Wrestling', NULL, 1),
(59, 0, 'Camp', 'Camp', NULL, 1),
(60, 0, 'Clinic', 'Clinic', NULL, 1),
(61, 0, 'Field Day', 'Field Day', NULL, 1),
(62, 0, 'Inservice', 'Inservice', NULL, 1),
(63, 0, 'Class', 'Class', NULL, 1),
(64, 0, 'Contest', 'Contest', NULL, 1),
(65, 0, 'Demonstration', 'Demonstration', NULL, 1),
(66, 0, 'Training', 'Training', NULL, 1),
(67, 0, 'Fair', 'Fair', NULL, 1),
(68, 0, 'Show', 'Show', NULL, 1),
(69, 0, 'Program', 'Program', NULL, 1),
(70, 0, 'Activity', 'Activity', NULL, 1),
(71, 0, 'Art Exhibition', 'Art Exhibition', NULL, 1),
(72, 0, 'Conversation', 'Conversation', NULL, 1),
(74, 0, 'Discussion', 'Discussion', NULL, 1),
(75, 0, 'Fundraiser', 'Fundraiser', NULL, 1),
(76, 0, 'Closure', 'Closure', NULL, 1),
(77, 0, 'Holiday', 'Holiday', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_has_eventtype`
--

DROP TABLE IF EXISTS `event_has_eventtype`;
CREATE TABLE `event_has_eventtype` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `eventtype_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_keyword`
--

DROP TABLE IF EXISTS `event_has_keyword`;
CREATE TABLE `event_has_keyword` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `keyword_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_sponsor`
--

DROP TABLE IF EXISTS `event_has_sponsor`;
CREATE TABLE `event_has_sponsor` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sponsor_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_isopento_audience`
--

DROP TABLE IF EXISTS `event_isopento_audience`;
CREATE TABLE `event_isopento_audience` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `audience_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_targets_audience`
--

DROP TABLE IF EXISTS `event_targets_audience`;
CREATE TABLE `event_targets_audience` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `audience_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facebook`
--

DROP TABLE IF EXISTS `facebook`;
CREATE TABLE `facebook` (
  `facebook_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `eventdatetime_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `page_name` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facebook_accounts`
--

DROP TABLE IF EXISTS `facebook_accounts`;
CREATE TABLE `facebook_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `facebook_account` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `access_token` varchar(100) NOT NULL DEFAULT ' ',
  `page_name` varchar(30) DEFAULT NULL,
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_events` tinyint(1) NOT NULL DEFAULT '0',
  `show_like_buttons` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keyword`
--

DROP TABLE IF EXISTS `keyword`;
CREATE TABLE `keyword` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `calendar_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `location` (`id`, `name`, `streetaddress1`, `streetaddress2`, `room`, `city`, `state`, `zip`, `mapurl`, `webpageurl`, `hours`, `directions`, `additionalpublicinfo`, `type`, `phone`, `standard`, `user_id`, `display_order`, `calendar_id`) VALUES
(1, '14th & Avery Parking Garage', '1111 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/14PG', NULL, NULL, NULL, '14PG', NULL, NULL, 1, NULL, NULL, NULL),
(2, '17th & R Parking Garage', '300 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/17PG', NULL, NULL, NULL, '17PG', NULL, NULL, 1, NULL, NULL, NULL),
(3, 'Facilities Management C', '1901 Y St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMC', NULL, NULL, NULL, 'FMC', NULL, NULL, 1, NULL, NULL, NULL),
(5, '501 Building', '501 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/501', NULL, NULL, NULL, '501', NULL, NULL, 1, NULL, NULL, NULL),
(8, 'Agricultural Communications Building', '3620 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ACB', NULL, NULL, NULL, 'ACB', NULL, NULL, 1, NULL, NULL, NULL),
(9, 'Agricultural Hall', '3550 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AGH', NULL, NULL, NULL, 'AGH', NULL, NULL, 1, NULL, NULL, NULL),
(11, 'Warehouse 2', '2105 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/W2', NULL, NULL, NULL, 'W2', NULL, NULL, 1, NULL, NULL, NULL),
(13, 'Agronomy & Horticulture Outstate Testing Laboratory', '3720 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHTL', NULL, NULL, NULL, 'AHTL', NULL, NULL, 1, NULL, NULL, NULL),
(14, 'Agronomy & Horticulture Physiology Building', '3710 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHPH', NULL, NULL, NULL, 'AHPH', NULL, NULL, 1, NULL, NULL, NULL),
(15, 'Alexander Building', '1410 Q St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ALEX', NULL, NULL, NULL, 'ALEX', NULL, NULL, 1, NULL, NULL, NULL),
(17, 'Andersen Hall', '200 Centennial Mall N', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ANDN', NULL, NULL, NULL, 'ANDN', NULL, NULL, 1, NULL, NULL, NULL),
(18, 'Andrews Hall', '625 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ANDR', NULL, NULL, NULL, 'ANDR', NULL, NULL, 1, NULL, NULL, NULL),
(23, 'Architecture Hall', '402 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ARCH', NULL, NULL, NULL, 'ARCH', NULL, NULL, 1, NULL, NULL, NULL),
(26, 'Avery Hall', '1144 T St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/AVH', NULL, NULL, NULL, 'AVH', NULL, NULL, 1, NULL, NULL, NULL),
(27, 'Barkley Memorial Center', '4075 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BKC', NULL, NULL, NULL, 'BKC', NULL, NULL, 1, NULL, NULL, NULL),
(28, 'Beadle Center', '1901 Vine St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BEAD', NULL, NULL, NULL, 'BEAD', NULL, NULL, 1, NULL, NULL, NULL),
(29, 'Behlen Laboratory', '500 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BEL', NULL, NULL, NULL, 'BEL', NULL, NULL, 1, NULL, NULL, NULL),
(31, 'Benton Hall', '1535 U St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BENH', NULL, NULL, NULL, 'BENH', NULL, NULL, 1, NULL, NULL, NULL),
(32, 'Bessey Hall', '1215 U St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/BESY', NULL, NULL, NULL, 'BESY', NULL, NULL, 1, NULL, NULL, NULL),
(36, 'Brace Laboratory', '510 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BL', NULL, NULL, NULL, 'BL', NULL, NULL, 1, NULL, NULL, NULL),
(37, 'Burnett Hall', '1220 T St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BURN', NULL, NULL, NULL, 'BURN', NULL, NULL, 1, NULL, NULL, NULL),
(38, 'Business Services Complex', '1700 Y St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BSC', NULL, NULL, NULL, 'BSC', NULL, NULL, 1, NULL, NULL, NULL),
(40, 'Canfield Administration Building North', '503 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ADMN', NULL, NULL, NULL, 'ADMN', NULL, NULL, 1, NULL, NULL, NULL),
(41, 'Canfield Administration Building South', '501 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ADMS', NULL, NULL, NULL, 'ADMS', NULL, NULL, 1, NULL, NULL, NULL),
(42, 'Chase Hall', '3605 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CHA', NULL, NULL, NULL, 'CHA', NULL, NULL, 1, NULL, NULL, NULL),
(44, 'Coliseum', '1350 Vine St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/COL', NULL, NULL, NULL, 'COL', NULL, NULL, 1, NULL, NULL, NULL),
(45, 'Louise Pound Hall ', '512 N 12th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LPH', NULL, NULL, NULL, 'LPH', NULL, NULL, 1, NULL, NULL, NULL),
(46, 'College of Dentistry', '4000 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/DENT', NULL, NULL, NULL, 'DENT', NULL, NULL, 1, NULL, NULL, NULL),
(47, 'Conservation & Survey Annex', '2000 N 34th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CSA', NULL, NULL, NULL, 'CSA', NULL, NULL, 1, NULL, NULL, NULL),
(48, 'Cook Pavilion', '845 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/COOK', NULL, NULL, NULL, 'COOK', NULL, NULL, 1, NULL, NULL, NULL),
(50, 'Devaney Sports Center', '1600 Court St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/DEV', NULL, NULL, NULL, 'DEV', NULL, NULL, 1, NULL, NULL, NULL),
(53, 'Entomology Greenhouse 2', '2110 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/EGR2', NULL, NULL, NULL, 'EGR2', NULL, NULL, 1, NULL, NULL, NULL),
(54, 'Entomology Greenhouse 3', '2120 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/EGR3', NULL, NULL, NULL, 'EGR3', NULL, NULL, 1, NULL, NULL, NULL),
(56, 'Facilities Management D', '1901 Y St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMD', NULL, NULL, NULL, 'FMD', NULL, NULL, 1, NULL, NULL, NULL),
(57, 'Facilities Management E', '1901 Y St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FME', NULL, NULL, NULL, 'FME', NULL, NULL, 1, NULL, NULL, NULL),
(58, 'Facilities Management F', '1901 Y St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMF', NULL, NULL, NULL, 'FMF', NULL, NULL, 1, NULL, NULL, NULL),
(59, 'Facilities Management Shops', '942 N 22nd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMS', NULL, NULL, NULL, 'FMS', NULL, NULL, 1, NULL, NULL, NULL),
(60, 'Fairfield Hall', '1545 U St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/FAIR', NULL, NULL, NULL, 'FAIR', NULL, NULL, 1, NULL, NULL, NULL),
(61, 'Family Resource Center', '1615 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FRC', NULL, NULL, NULL, 'FRC', NULL, NULL, 1, NULL, NULL, NULL),
(63, 'Filley Hall', '3720 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/FYH', NULL, NULL, NULL, 'FYH', NULL, NULL, 1, NULL, NULL, NULL),
(64, 'Food Industry Complex', '3720 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/FOOD', NULL, NULL, NULL, 'FOOD', NULL, NULL, 1, NULL, NULL, NULL),
(65, 'Forage Research Laboratory - USDA', '3870 Center Dr', NULL, NULL, 'Lincoln', 'NE', '68510', 'https://maps.unl.edu/FORL', NULL, NULL, NULL, 'FORL', NULL, NULL, 1, NULL, NULL, NULL),
(67, 'Hamilton Hall', '639 N 12th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/HAH', NULL, NULL, NULL, 'HAH', NULL, NULL, 1, NULL, NULL, NULL),
(68, 'Hardin Hall', '3310 Holdrege St', NULL, NULL, 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/HARH', NULL, NULL, NULL, 'HARH', NULL, NULL, 1, NULL, NULL, NULL),
(70, 'Haymarket Park Baseball Stadium Complex', '403 Line Drive Cir', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HAYB', NULL, NULL, NULL, 'HAYB', NULL, NULL, 1, NULL, NULL, NULL),
(71, 'Haymarket Park Softball Stadium Complex', '400 Line Drive Cir', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HAYS', NULL, NULL, NULL, 'HAYS', NULL, NULL, 1, NULL, NULL, NULL),
(73, 'Henzlik Hall', '1430 Vine St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HENZ', NULL, NULL, NULL, 'HENZ', NULL, NULL, 1, NULL, NULL, NULL),
(75, 'Hewit Place', '1155 Q St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HEWP', NULL, NULL, NULL, 'HEWP', NULL, NULL, 1, NULL, NULL, NULL),
(77, 'Plant Pathology Greenhouse', '2075 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PPG', NULL, NULL, NULL, 'PPG', NULL, NULL, 1, NULL, NULL, NULL),
(78, 'Plant Science Teaching Greenhouse', '3855 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PSTG', NULL, NULL, NULL, 'PSTG', NULL, NULL, 1, NULL, NULL, NULL),
(79, 'Teaching Greenhouse West', '3850 Center Dr', NULL, NULL, 'Lincoln', 'NE', '68510', 'https://maps.unl.edu/TGW', NULL, NULL, NULL, 'TGW', NULL, NULL, 1, NULL, NULL, NULL),
(80, 'Teaching Greenhouse East', '3850 Center Dr', NULL, NULL, 'Lincoln', 'NE', '68510', 'https://maps.unl.edu/TGE', NULL, NULL, NULL, 'TGE', NULL, NULL, 1, NULL, NULL, NULL),
(81, 'Insectary Building', '3865 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/INSB', NULL, NULL, NULL, 'INSB', NULL, NULL, 1, NULL, NULL, NULL),
(83, 'Kauffman Academic Residential Center', '630 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KAUF', NULL, NULL, NULL, 'KAUF', NULL, NULL, 1, NULL, NULL, NULL),
(84, 'Keim Hall', '1825 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/KEIM', NULL, NULL, NULL, 'KEIM', NULL, NULL, 1, NULL, NULL, NULL),
(85, 'Kiesselbach Crops Research Laboratory', '1870 N 37th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/KCR', NULL, NULL, NULL, 'KCR', NULL, NULL, 1, NULL, NULL, NULL),
(86, 'Kimball Recital Hall', '1113 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KRH', NULL, NULL, NULL, 'KRH', NULL, NULL, 1, NULL, NULL, NULL),
(92, 'Larsen Tractor Museum', '1925 N 37th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LTM', NULL, NULL, NULL, 'LTM', NULL, NULL, 1, NULL, NULL, NULL),
(93, 'Leverton Hall', '1700 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LEV', NULL, NULL, NULL, 'LEV', NULL, NULL, 1, NULL, NULL, NULL),
(95, 'Lied Center for Performing Arts', '301 N 12th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LIED', NULL, NULL, NULL, 'LIED', NULL, NULL, 1, NULL, NULL, NULL),
(97, 'Love Library North & Link', '1300 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LLN', NULL, NULL, NULL, 'LLN', NULL, NULL, 1, NULL, NULL, NULL),
(98, 'Love Library South', '1248 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LLS', NULL, NULL, NULL, 'LLS', NULL, NULL, 1, NULL, NULL, NULL),
(101, 'Manter Hall', '1101 T St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MANT', NULL, NULL, NULL, 'MANT', NULL, NULL, 1, NULL, NULL, NULL),
(103, 'McCollum Hall', '1875 N 42nd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LAW', NULL, NULL, NULL, 'LAW', NULL, NULL, 1, NULL, NULL, NULL),
(104, 'Pershing Military & Naval Science Building', '1360 Vine St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/M&N', NULL, NULL, NULL, 'M&N', NULL, NULL, 1, NULL, NULL, NULL),
(106, 'Morrill Hall', '1335 U St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MORR', NULL, NULL, NULL, 'MORR', NULL, NULL, 1, NULL, NULL, NULL),
(107, 'Mussehl Hall', '1915 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MUSH', NULL, NULL, NULL, 'MUSH', NULL, NULL, 1, NULL, NULL, NULL),
(108, 'National Agroforestry Center - USDA', '1945 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NAC', NULL, NULL, NULL, 'NAC', NULL, NULL, 1, NULL, NULL, NULL),
(110, 'Nebraska East Union', '1705 Arbor Dr', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NEU', NULL, NULL, NULL, 'NEU', NULL, NULL, 1, NULL, NULL, NULL),
(111, 'Nebraska Hall', '900 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NH', NULL, NULL, NULL, 'NH', NULL, NULL, 1, NULL, NULL, NULL),
(112, 'Nebraska Statewide Arboretum Greenhouse', '2150 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NSAG', NULL, NULL, NULL, 'NSAG', NULL, NULL, 1, NULL, NULL, NULL),
(113, 'Nebraska Union', '1400 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NU', NULL, NULL, NULL, 'NU', NULL, NULL, 1, NULL, NULL, NULL),
(114, 'Terry M. Carpenter Telecommunications Center', '1800 N 33rd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/TELC', NULL, NULL, NULL, 'TELC', NULL, NULL, 1, NULL, NULL, NULL),
(115, 'Oldfather Hall', '660 N 12th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/OLDH', NULL, NULL, NULL, 'OLDH', NULL, NULL, 1, NULL, NULL, NULL),
(117, 'Osborne Athletic Complex', '800 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MSTD', NULL, NULL, NULL, 'MSTD', NULL, NULL, 1, NULL, NULL, NULL),
(118, 'Othmer Hall', '820 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/OTHM', NULL, NULL, NULL, 'OTHM', NULL, NULL, 1, NULL, NULL, NULL),
(123, 'Agronomy & Horticulture Greenhouse 4', '3855 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG4', NULL, NULL, NULL, 'AHG4', NULL, NULL, 1, NULL, NULL, NULL),
(124, 'Plant Sciences Hall', '1875 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PLSH', NULL, NULL, NULL, 'PLSH', NULL, NULL, 1, NULL, NULL, NULL),
(131, 'Richards Hall', '560 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RH', NULL, NULL, NULL, 'RH', NULL, NULL, 1, NULL, NULL, NULL),
(133, 'Sapp Recreation Facility', '841 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CREC', NULL, NULL, NULL, 'CREC', NULL, NULL, 1, NULL, NULL, NULL),
(134, 'Scott Engineering Center', '844 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SEC', NULL, NULL, NULL, 'SEC', NULL, NULL, 1, NULL, NULL, NULL),
(136, 'Seaton Hall', '1525 U St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SEH', NULL, NULL, NULL, 'SEH', NULL, NULL, 1, NULL, NULL, NULL),
(138, 'Sheldon Museum of Art', '451 N 12th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/SHEL', NULL, NULL, NULL, 'SHEL', NULL, NULL, 1, NULL, NULL, NULL),
(140, 'Stadium Drive Parking Garage', '625 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/SDPG', NULL, NULL, NULL, 'SDPG', NULL, NULL, 1, NULL, NULL, NULL),
(141, 'Stadium East', '1100 T St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STE', NULL, NULL, NULL, 'STE', NULL, NULL, 1, NULL, NULL, NULL),
(147, 'Schorr Center', '1100 T St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/SHOR', NULL, NULL, NULL, 'SHOR', NULL, NULL, 1, NULL, NULL, NULL),
(149, 'Stewart Seed Laboratory', '2101 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SSL', NULL, NULL, NULL, 'SSL', NULL, NULL, 1, NULL, NULL, NULL),
(151, 'Teachers College Hall', '1400 Vine St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/TEAC', NULL, NULL, NULL, 'TEAC', NULL, NULL, 1, NULL, NULL, NULL),
(152, 'Temple Building', '1209 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/TEMP', NULL, NULL, NULL, 'TEMP', NULL, NULL, 1, NULL, NULL, NULL),
(153, 'Bio-Fiber Development Laboratory', '1605 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BDL', NULL, NULL, NULL, 'BDL', NULL, NULL, 1, NULL, NULL, NULL),
(155, 'Transportation Services', '1931 N Antelope Valley Pky', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/TRAN', NULL, NULL, NULL, 'TRAN', NULL, NULL, 1, NULL, NULL, NULL),
(156, 'University Health Center', '1500 U St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/UHC', NULL, NULL, NULL, 'UHC', NULL, NULL, 1, NULL, NULL, NULL),
(160, 'Utility Plant, East Campus', '1935 N 37th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ECUP', NULL, NULL, NULL, 'ECUP', NULL, NULL, 1, NULL, NULL, NULL),
(162, 'Varner Hall', '3835 Holdrege St', NULL, NULL, 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/VARH', NULL, NULL, NULL, 'VARH', NULL, NULL, 1, NULL, NULL, NULL),
(166, 'Water Sciences Laboratory', '1840 N 37th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/WL', NULL, NULL, NULL, 'WL', NULL, NULL, 1, NULL, NULL, NULL),
(169, 'Welpton Courtroom Building', '1875 N 42nd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/WELC', NULL, NULL, NULL, 'WELC', NULL, NULL, 1, NULL, NULL, NULL),
(170, 'Westbrook Music Building', '1104 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WMB', NULL, NULL, NULL, 'WMB', NULL, NULL, 1, NULL, NULL, NULL),
(174, 'Woods Art Building', '1140 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WAB', NULL, NULL, NULL, 'WAB', NULL, NULL, 1, NULL, NULL, NULL),
(204, 'Memorial Stadium', 'One Memorial Stadium Drive', NULL, NULL, 'Lincoln', 'NE', '68588', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(257, 'Champions Club', '707 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/NECH', NULL, NULL, NULL, 'NECH', NULL, NULL, 1, NULL, NULL, NULL),
(1458, 'Wick Alumni Center', '1520 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WICK', NULL, NULL, NULL, 'WICK', NULL, NULL, 1, NULL, NULL, NULL),
(2843, 'Entomology Hall', '1700 East Campus Mall', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ENTO', NULL, NULL, NULL, 'ENTO', NULL, NULL, 1, NULL, NULL, NULL),
(5077, 'Jackie Gaughan Multicultural Center', '1505 S St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/GAUN', NULL, 'Regular semester hours Mon-Thu 8 a.m. - 11 p.m. Fri 8 a.m. - 10 p.m. Sat 10 a.m. - 10 p.m. Sun 12 p.m. - 10 p.m. Finals Week Mon-Thu 7 a.m. - 11 p.m. Fri 7 a.m. - 6 p.m. Summer Sessions Mon-Fri 8 a.m. - 5 p.m. Closed Sat-Sun', NULL, 'GAUN', NULL, '(402) 472-5500', 1, NULL, NULL, NULL),
(6643, 'Morrison Center', '4240 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MOLR', NULL, NULL, NULL, 'MOLR', NULL, '402-472-4560', 1, NULL, NULL, NULL),
(9669, 'Adams County Extension', '300 N. St. Joseph Ave Room 103', 'PO Box 30', NULL, 'Hastings', 'NE', '68902-0030', NULL, NULL, NULL, NULL, NULL, NULL, '402-461-7209', 1, NULL, 1, NULL),
(9670, 'Antelope County Extension', '501 Main', 'Suite B9', NULL, 'Neligh', 'NE', '68756-1475', NULL, NULL, NULL, NULL, NULL, NULL, '402-887-5414', 1, NULL, 1, NULL),
(9671, 'Boone County Extension', '222 South 4th', '', NULL, 'Albion', 'NE', '68620-1247', NULL, NULL, NULL, NULL, NULL, NULL, '402-395-2158', 1, NULL, 1, NULL),
(9672, 'Nance County Extension', '209 Esther Street-Courthouse', 'PO Box 130', NULL, 'Fullerton', 'NE', '68638-0130', NULL, NULL, NULL, NULL, NULL, NULL, '308-536-2691', 1, NULL, 1, NULL),
(9673, 'Box Butte County Extension', '415 Black Hills Avenue', '', NULL, 'Alliance', 'NE', '69301-3243', NULL, NULL, NULL, NULL, NULL, NULL, '308-762-5616', 1, NULL, 1, NULL),
(9674, 'Brown-Rock-KeyaPaha County Extension', '148 West 4th Street', '', NULL, 'Ainsworth', 'NE', '69210-1696', NULL, NULL, NULL, NULL, NULL, NULL, '402-387-2213', 1, NULL, 1, NULL),
(9675, 'Buffalo County Extension', '1400 E. 34th (Fairgrounds)', '', NULL, 'Kearney', 'NE', '68847-3992', NULL, NULL, NULL, NULL, NULL, NULL, '308-236-1235', 1, NULL, 1, NULL),
(9676, 'Burt County Extension', '111 North 13th Street', 'Suite 6', NULL, 'Tekamah', 'NE', '68061-1098', NULL, NULL, NULL, NULL, NULL, NULL, '402-374-2929', 1, NULL, 1, NULL),
(9677, 'Butler County Extension', '451 N. 5th Street', '', NULL, 'David City', 'NE', '68632-1666', NULL, NULL, NULL, NULL, NULL, NULL, '402-367-7410', 1, NULL, 1, NULL),
(9678, 'Cass County Extension', '8400 144th Street', 'Suite 100', NULL, 'Weeping Water', 'NE', '68463-1932', NULL, NULL, NULL, NULL, NULL, NULL, '402-267-2205', 1, NULL, 1, NULL),
(9679, 'Cedar County Extension', '101 East Centre', 'PO Box 368', NULL, 'Hartington', 'NE', '68739-0368', NULL, NULL, NULL, NULL, NULL, NULL, '402-254-6821', 1, NULL, 1, NULL),
(9680, 'Valley County Extension', '801 S Street', 'Suite 1 - Fairgrounds', NULL, 'Ord', 'NE', '68862-1857', NULL, NULL, NULL, NULL, NULL, NULL, '308-728-5071', 1, NULL, 1, NULL),
(9681, 'Howard County Extension', '612 Indian Street', 'Suite 1', NULL, 'St. Paul', 'NE', '68873-1642', NULL, NULL, NULL, NULL, NULL, NULL, '308-754-5422', 1, NULL, 1, NULL),
(9682, 'Sherman County Extension', '630 O Street-Courthouse', 'PO Box 459', NULL, 'Loup City', 'NE', '68853-1557', NULL, NULL, NULL, NULL, NULL, NULL, '308-745-1518', 1, NULL, 1, NULL),
(9683, 'Greeley County Extension', 'Corner of O\'Neill & Kildare', 'PO Box 290', NULL, 'Greeley', 'NE', '68842-0290', NULL, NULL, NULL, NULL, NULL, NULL, '308-428-2835', 1, NULL, 1, NULL),
(9684, 'Blaine/Grant/Hooker/Thomas County Extension', '503 Main Street', 'PO Box 148', NULL, 'Thedford', 'NE', '69166-0148', NULL, NULL, NULL, NULL, NULL, NULL, '308-645-2267', 1, NULL, 1, NULL),
(9685, 'Cherry County Extension', '365 N. Main Street', 'Suite 3', NULL, 'Valentine', 'NE', '69201', NULL, NULL, NULL, NULL, NULL, NULL, '402-376-1850', 1, NULL, 1, NULL),
(9686, 'Clay County Extension', '111 West Fairfield', '', NULL, 'Clay Center', 'NE', '68933-1499', NULL, NULL, NULL, NULL, NULL, NULL, '402-762-3644', 1, NULL, 1, NULL),
(9687, 'Colfax County Extension', '466 Road 10', 'PO Box 389', NULL, 'Schuyler', 'NE', '68661-0389', NULL, NULL, NULL, NULL, NULL, NULL, '402-352-3821', 1, NULL, 1, NULL),
(9688, 'Cuming County Extension', '200 South Lincoln Street', '', NULL, 'West Point', 'NE', '68788-1848', NULL, NULL, NULL, NULL, NULL, NULL, '402-372-6006', 1, NULL, 1, NULL),
(9689, 'Custer County Extension', '431 South 10 Avenue', '', NULL, 'Broken Bow', 'NE', '68822-2099', NULL, NULL, NULL, NULL, NULL, NULL, '308-872-6831', 1, NULL, 1, NULL),
(9690, 'Dakota County Extension', '1505 Broadway', 'PO Box 129', NULL, 'Dakota City', 'NE', '68731-0129', NULL, NULL, NULL, NULL, NULL, NULL, '402-987-2140', 1, NULL, 1, NULL),
(9691, 'Dawes County Extension', '250 Main Street', 'Suite 8', NULL, 'Chadron', 'NE', '69337', NULL, NULL, NULL, NULL, NULL, NULL, '308-432-3373', 1, NULL, 1, NULL),
(9692, 'Dawson County Extension', '1002 Plum Creek Parkway', 'PO Box 757', NULL, 'Lexington', 'NE', '68850-0757', NULL, NULL, NULL, NULL, NULL, NULL, '308-324-5501', 1, NULL, 1, NULL),
(9693, 'Garden County Extension', '611 Main Street', 'PO Box 350', NULL, 'Oshkosh', 'NE', '69154-0350', NULL, NULL, NULL, NULL, NULL, NULL, '308-772-3311', 1, NULL, 1, NULL),
(9694, 'Deuel County Extension', '171 Vincent', 'PO Box 625', NULL, 'Chappell', 'NE', '69129-0625', NULL, NULL, NULL, NULL, NULL, NULL, '308-874-2705', 1, NULL, 1, NULL),
(9695, 'Dixon County Extension', '57905 866 Road', '', NULL, 'Concord', 'NE', '68728-2828', NULL, NULL, NULL, NULL, NULL, NULL, '402-584-2234', 1, NULL, 1, NULL),
(9696, 'Dodge County Extension', '1206 W 23rd Street', '', NULL, 'Fremont', 'NE', '68025-2504', NULL, NULL, NULL, NULL, NULL, NULL, '402-727-2775', 1, NULL, 1, NULL),
(9697, 'Douglas-Sarpy County Extension', '8015 W Center Road', '', NULL, 'Omaha', 'NE', '68124-3175', NULL, NULL, NULL, NULL, NULL, NULL, '402-444-7804', 1, NULL, 1, NULL),
(9698, 'Fillmore County Extension', '972 G Street', '', NULL, 'Geneva', 'NE', '68361-2005', NULL, NULL, NULL, NULL, NULL, NULL, '402-223-1384', 1, NULL, 1, NULL),
(9699, 'Frontier County Extension', '404 East 7th Street', 'Suite 2', NULL, 'Curtis', 'NE', '69025-9527', NULL, NULL, NULL, NULL, NULL, NULL, '308-367-4424', 1, NULL, 1, NULL),
(9700, 'Furnas County Extension', '912 R Street - Courthouse', 'PO Box 367', NULL, 'Beaver City', 'NE', '68926-0367', NULL, NULL, NULL, NULL, NULL, NULL, '308-268-3105', 1, NULL, 1, NULL),
(9701, 'Gage County Extension', '1115 West Scott', '', NULL, 'Beatrice', 'NE', '68310-3514', NULL, NULL, NULL, NULL, NULL, NULL, '402-223-1384', 1, NULL, 1, NULL),
(9702, 'Garfield-Loup-Wheeler County Extension', '250 S 8th Avenue', 'PO Box 638', NULL, 'Burwell', 'NE', '68823-0638', NULL, NULL, NULL, NULL, NULL, NULL, '308-346-4200', 1, NULL, 1, NULL),
(9703, 'Hall County Extension', '3180 W Highway 34', '', NULL, 'Grand Island', 'NE', '68801-7279', NULL, NULL, NULL, NULL, NULL, NULL, '308-385-5088', 1, NULL, 1, NULL),
(9704, 'Hamilton County Extension', '1111 13th Street-Suite 6 Courthouse', 'PO Box 308', NULL, 'Aurora', 'NE', '68818-0308', NULL, NULL, NULL, NULL, NULL, NULL, '402-694-6174', 1, NULL, 1, NULL),
(9705, 'Harlan County Extension', '706 Second Street', 'PO Box 258', NULL, 'Alma', 'NE', '68920-0258', NULL, NULL, NULL, NULL, NULL, NULL, '308-928-2119', 1, NULL, 1, NULL),
(9706, 'Boyd County Extension', '401 Thayer Street', 'PO Box 108', NULL, 'Butte', 'NE', '68722-0108', NULL, NULL, NULL, NULL, NULL, NULL, '402-775-2491', 1, NULL, 1, NULL),
(9707, 'Holt County Extension', '128 N 6th Street', 'Suite 100', NULL, 'O\'Neill', 'NE', '68763-1616', NULL, NULL, NULL, NULL, NULL, NULL, '402-336-2760', 1, NULL, 1, NULL),
(9708, 'Jefferson County Extension', '517 F Street', '', NULL, 'Fairbury', 'NE', '68352-3487', NULL, NULL, NULL, NULL, NULL, NULL, '402-729-3487', 1, NULL, 1, NULL),
(9709, 'Johnson County Extension', '3rd & Broadway - Courthouse', 'PO Box 779', NULL, 'Tecumseh', 'NE', '68450-0779', NULL, NULL, NULL, NULL, NULL, NULL, '402-335-3669', 1, NULL, 1, NULL),
(9710, 'Franklin County Extension', '405 15th Avenue - Courthouse', 'PO Box 266', NULL, 'Franklin', 'NE', '68939-0266', NULL, NULL, NULL, NULL, NULL, NULL, '308-425-6277', 1, NULL, 1, NULL),
(9711, 'Kearney County Extension', '424 N Colorado', 'PO Box 31', NULL, 'Minden', 'NE', '68959-0031', NULL, NULL, NULL, NULL, NULL, NULL, '308-832-0645', 1, NULL, 1, NULL),
(9712, 'Keith-Arthur County Extension', '511 North Spruce', 'Room 203', NULL, 'Ogallala', 'NE', '69153-0450', NULL, NULL, NULL, NULL, NULL, NULL, '308-284-6051', 1, NULL, 1, NULL),
(9713, 'Cheyenne County Extension', '920 Jackson Street', 'PO Box 356', NULL, 'Sidney', 'NE', '69162-0356', NULL, NULL, NULL, NULL, NULL, NULL, '308-254-4455', 1, NULL, 1, NULL),
(9714, 'Kimball-Banner County Extension', '209 East 3rd', '', NULL, 'Kimball', 'NE', '69145-1433', NULL, NULL, NULL, NULL, NULL, NULL, '308-235-3122', 1, NULL, 1, NULL),
(9715, 'Knox County Extension', '308 Bridge Street', 'PO Box 45', NULL, 'Center', 'NE', '68724-0045', NULL, NULL, NULL, NULL, NULL, NULL, '402-288-5611', 1, NULL, 1, NULL),
(9716, 'Lancaster County Extension', '444 Cherrycreek Road', 'Suite A', NULL, 'Lincoln', 'NE', '68528-1591', NULL, NULL, NULL, NULL, NULL, NULL, '402-441-7180', 1, NULL, 1, NULL),
(9717, 'Lincoln-Logan-McPherson County Extension', '348 West State Farm Road', '', NULL, 'North Platte', 'NE', '69101-7751', NULL, NULL, NULL, NULL, NULL, NULL, '308-532-2683', 1, NULL, 1, NULL),
(9718, 'Madison County Extension', '1305 South 13th Street', NULL, NULL, 'Norfolk', 'NE', '68701-0813', NULL, NULL, NULL, NULL, NULL, NULL, '402-370-4040', 1, NULL, 1, NULL),
(9719, 'Merrick County Extension', '1510 18th Street', 'PO Box 27', NULL, 'Central City', 'NE', '68826-0027', NULL, NULL, NULL, NULL, NULL, NULL, '308-946-3843', 1, NULL, 1, NULL),
(9720, 'Nemaha County Extension', '1824 N Street', 'Suite102', NULL, 'Auburn', 'NE', '68305-2395', NULL, NULL, NULL, NULL, NULL, NULL, '402-274-4755', 1, NULL, 1, NULL),
(9721, 'Otoe County Extension', '180 Chestnut', 'PO Box 160', NULL, 'Syracuse', 'NE', '68446-0160', NULL, NULL, NULL, NULL, NULL, NULL, '402-269-2301', 1, NULL, 1, NULL),
(9722, 'Pawnee County Extension', '625 6th Street - Courthouse', 'PO Box 391', NULL, 'Pawnee City', 'NE', '68420-0391', NULL, NULL, NULL, NULL, NULL, NULL, '402-852-2970', 1, NULL, 1, NULL),
(9723, 'Chase County Extension', '816 Douglas', 'PO Box 640', NULL, 'Imperial', 'NE', '69033-0640', NULL, NULL, NULL, NULL, NULL, NULL, '308-882-4731', 1, NULL, 1, NULL),
(9724, 'Perkins County Extension', '200 Lincoln Avenue', 'PO Box 99', NULL, 'Grant', 'NE', '69140-0099', NULL, NULL, NULL, NULL, NULL, NULL, '308-352-7580', 1, NULL, 1, NULL),
(9725, 'Gosper County Extension', '507 Smith Avenue', 'PO Box 146', NULL, 'Elwood', 'NE', '68937-0146', NULL, NULL, NULL, NULL, NULL, NULL, '308-785-2390', 1, NULL, 1, NULL),
(9726, 'Phelps County Extension', '1308 Second Street', '', NULL, 'Holdrege', 'NE', '68949-2803', NULL, NULL, NULL, NULL, NULL, NULL, '308-995-4222', 1, NULL, 1, NULL),
(9727, 'Pierce County Extension', '111 W. Court Street', 'Room 13', NULL, 'Pierce', 'NE', '68767-1224', NULL, NULL, NULL, NULL, NULL, NULL, '402-329-4821', 1, NULL, 1, NULL),
(9728, 'Platte County Extension', '2715 13th St.', '', NULL, 'Columbus', 'NE', '68601-4916', NULL, NULL, NULL, NULL, NULL, NULL, '402-563-4901', 1, NULL, 1, NULL),
(9729, 'Polk County Extension', '400 Hawkeye', 'PO Box 215', NULL, 'Osceola', 'NE', '68651-0215', NULL, NULL, NULL, NULL, NULL, NULL, '402-747-2321', 1, NULL, 1, NULL),
(9730, 'Red Willow County Extension', '1400 West 5th Street', 'Ste 2', NULL, 'McCook', 'NE', '69001-2593', NULL, NULL, NULL, NULL, NULL, NULL, '308-345-3390', 1, NULL, 1, NULL),
(9731, 'Richardson County Extension', '1700 Stone - Courthouse', '', NULL, 'Falls City', 'NE', '68355-2033', NULL, NULL, NULL, NULL, NULL, NULL, '402-245-4324', 1, NULL, 1, NULL),
(9732, 'Saline County Extension', '306 West Third', 'PO Box 978', NULL, 'Wilber', 'NE', '68465-0978', NULL, NULL, NULL, NULL, NULL, NULL, '402-821-2151', 1, NULL, 1, NULL),
(9733, 'Saunders County Extension', '1071 County Road G', 'Room B', NULL, 'Ithaca', 'NE', '68033-2234', NULL, NULL, NULL, NULL, NULL, NULL, '402-624-8030', 1, NULL, 1, NULL),
(9734, 'Scotts Bluff County Extension', '4502 Avenue I', '', NULL, 'Scottsbluff', 'NE', '69361-4939', NULL, NULL, NULL, NULL, NULL, NULL, '308-632-1480', 1, NULL, 1, NULL),
(9735, 'Morrill County Extension', '514 Main Street', '', NULL, 'Bridgeport', 'NE', '69336-0490', NULL, NULL, NULL, NULL, NULL, NULL, '308-262-1022', 1, NULL, 1, NULL),
(9736, 'Seward County Extension', '322 S 14th Street', '', NULL, 'Seward', 'NE', '68434', NULL, NULL, NULL, NULL, NULL, NULL, '402-643-2981', 1, NULL, 1, NULL),
(9737, 'Sheridan County Extension', '105 Loofborrow Street', 'PO Box 329', NULL, 'Rushville', 'NE', '69360-0329', NULL, NULL, NULL, NULL, NULL, NULL, '308-327-2312', 1, NULL, 1, NULL),
(9738, 'Sioux County Extension', '325 Main Street', 'PO Box 277', NULL, 'Harrison', 'NE', '69346-0277', NULL, NULL, NULL, NULL, NULL, NULL, '308-668-2428', 1, NULL, 1, NULL),
(9739, 'Nuckolls County Extension', '825 S Main', '', NULL, 'Nelson', 'NE', '68961-8113', NULL, NULL, NULL, NULL, NULL, NULL, '402-225-2381', 1, NULL, 1, NULL),
(9740, 'Thayer County Extension', '225 North 4th', 'Room 104', NULL, 'Hebron', 'NE', '68370-1598', NULL, NULL, NULL, NULL, NULL, NULL, '402-768-7212', 1, NULL, 1, NULL),
(9741, 'Hitchcock County Extension', '229 East D', 'PO Box 248', NULL, 'Trenton', 'NE', '69044-0248', NULL, NULL, NULL, NULL, NULL, NULL, '308-334-5333', 1, NULL, 1, NULL),
(9742, 'Dundy County Extension', '112 7th Avenue West', 'PO Box 317', NULL, 'Benkelman', 'NE', '69021-0317', NULL, NULL, NULL, NULL, NULL, NULL, '308-423-2021', 1, NULL, 1, NULL),
(9743, 'Hayes County Extension', '505 Troth Street', 'PO Box 370', NULL, 'Hayes Center', 'NE', '69032-0370', NULL, NULL, NULL, NULL, NULL, NULL, '308-286-3312', 1, NULL, 1, NULL),
(9744, 'Stanton County Extension', '302 6th Street', '', NULL, 'Stanton', 'NE', '68779', NULL, NULL, NULL, NULL, NULL, NULL, '402-439-2231', 1, NULL, 1, NULL),
(9745, 'Thurston County Extension', '415 Main Street', 'PO Box 665', NULL, 'Pender', 'NE', '68047', NULL, NULL, NULL, NULL, NULL, NULL, '402-385-6041', 1, NULL, 1, NULL),
(9746, 'Washington County Extension', '597 Grant Street', 'Suite 200', NULL, 'Blair', 'NE', '68008', NULL, NULL, NULL, NULL, NULL, NULL, '402-426-9455', 1, NULL, 1, NULL),
(9747, 'Wayne County Extension', '510 N Pearl Street', 'Suite C', NULL, 'Wayne', 'NE', '68787-1939', NULL, NULL, NULL, NULL, NULL, NULL, '402-375-3310', 1, NULL, 1, NULL),
(9748, 'Webster County Extension', '621 N Cedar', '', NULL, 'Red Cloud', 'NE', '68970-2397', NULL, NULL, NULL, NULL, NULL, NULL, '402-746-3417', 1, NULL, 1, NULL),
(9749, 'York County Extension', '2345 Nebraska Avenue', '', NULL, 'York', 'NE', '68467-1104', NULL, NULL, NULL, NULL, NULL, NULL, '402-362-5508', 1, NULL, 1, NULL),
(9750, 'Northeast Research and Extension Center', '601 East Benjamin Avenue', 'Suite 104', NULL, 'Norfolk', 'NE', '68701-0812', NULL, NULL, NULL, NULL, NULL, NULL, '402-370-4000', 1, NULL, 1, NULL),
(9751, 'Panhandle Research and Extension Center', '4502 Avenue I', '', NULL, 'Scottsbluff', 'NE', '69361-4939', NULL, NULL, NULL, NULL, NULL, NULL, '308-632-1230', 1, NULL, 1, NULL),
(9752, 'Southeast Research and Extension Center', '1071 County Road G ', 'Room D', NULL, 'Ithaca', 'NE', '68033-2234', NULL, NULL, NULL, NULL, NULL, NULL, '402-624-8037', 1, NULL, 1, NULL),
(9753, 'West Central Research and Extension Center', '402 W. State Farm Rd.', '', NULL, 'North Platte', 'NE', '69101-7751', NULL, NULL, NULL, NULL, NULL, NULL, '308-696-6740', 1, NULL, 1, NULL),
(9754, 'UNL - Barta Brothers Ranch', '148 West 4th Street', '', NULL, 'Anisworth', 'NE', '69210', NULL, NULL, NULL, NULL, NULL, NULL, '402-273-2030', 1, NULL, 1, NULL),
(9755, 'Gudmundsen Sandhills Laboratory', '45089 Gudmundsen Rd ', '', NULL, 'Whitman', 'NE', '69366-4705', NULL, NULL, NULL, NULL, NULL, NULL, '', 1, NULL, 1, NULL),
(9756, 'Haskell Agricultural Laboratory', '57905 866 Road', '', NULL, 'Concord', 'NE', '68728', NULL, NULL, NULL, NULL, NULL, NULL, '402-584-2261', 1, NULL, 1, NULL),
(9757, 'Eastern Nebraska Research and Extension Center', '1071 County Road G ', '', NULL, 'Ithaca', 'NE', '68033', NULL, NULL, NULL, NULL, NULL, NULL, '402-624-8037', 1, NULL, 1, NULL),
(9758, 'Kimmel Education & Research Center', '5985 G Road', '', NULL, 'Nebraska City', 'NE', '68410', NULL, NULL, NULL, NULL, NULL, NULL, '402-873-3166', 1, NULL, 1, NULL),
(9759, 'Lifelong Learning Center', '601 East Benjamin Avenue', '', NULL, 'Norfolk', 'NE', '68701-0812', NULL, NULL, NULL, NULL, NULL, NULL, '402-370-4000', 1, NULL, 1, NULL),
(9760, 'South Central Agricultural Laboratory', '842 Road 313', 'PO Box 313', NULL, 'Clay Center', 'NE', '68933-0066', NULL, NULL, NULL, NULL, NULL, NULL, '402-762-4403', 1, NULL, 1, NULL),
(9761, 'Nebraska College of Technical Agriculture', '404 E. 7th Street', '', NULL, 'Curtis', 'NE', '69025', NULL, NULL, NULL, NULL, NULL, NULL, '308-367-4124', 1, NULL, 1, NULL),
(9865, 'Nebraska Innovation Campus Conference Center', '2021 Transformation Drive', NULL, 'Auditorium', 'Lincoln', 'Ne', '68508', 'https://goo.gl/maps/2dWxd', NULL, NULL, NULL, NULL, 'United States', '4024727080', 1, NULL, NULL, NULL),
(10145, 'Cedar Point Biological Station', '170 Cedar Point Drive', NULL, NULL, 'Rural Ogallala', 'NE', '69153', 'https://www.google.com/maps/place/Cedar+Point+Biological+Station/@41.209655,-101.647739,13z/data=!4m2!3m1!1s0x8776e6f7cf486c89:0x171df262dfeac092?hl=en', 'http://cedarpoint.unl.edu/', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL),
(10197, 'International Quilt Study Center & Museum', '1523 N. 33rd St.', NULL, NULL, 'Lincoln', 'NE', '68583', NULL, 'www.quiltstudy.org', NULL, '33rd and Holdrege streets', NULL, NULL, '4024726549', 1, NULL, NULL, NULL),
(12411, 'Johnny Carson Theater', '11th & Q Streets', NULL, NULL, 'Lincoln', 'NE', '68588', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(14616, 'College of Business - Howard L. Hawks Hall', '730 N. 14th St.', NULL, NULL, 'Lincoln', 'NE', '68588', 'http://maps.unl.edu/HLH', 'http://cba.unl.edu', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(16290, 'Adele Coryell Hall Learning Commons', '13th and R', 'Love Library', NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LLN', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(19493, 'Robert E. Knoll Residential Center ', '440 N 17th ', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KNOL', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(19494, 'University Suites', '1780 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/UNST', 'https://housing.unl.edu/university-suites', NULL, NULL, 'UNST', NULL, '(402) 472-7179', 1, NULL, NULL, NULL),
(19497, 'Willa Cather Dining Complex', '530 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68588-1600', 'https://maps.unl.edu/WCDC', 'https://conferenceservices.unl.edu/willa-cather-dining-complex', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(23922, 'Campus Recreation Center', '841 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'http://maps.unl.edu/#CREC', NULL, NULL, NULL, 'CREC', NULL, NULL, 1, NULL, NULL, NULL),
(24099, '17th Street Green Space', '530 N 17th St\r\n', NULL, NULL, 'Lincoln', 'NE', '68588', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(28977, 'Dinsdale Family Learning Commons', '1625 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/DINS', NULL, NULL, NULL, 'DINS', NULL, NULL, 1, NULL, NULL, NULL),
(28979, 'Love Memorial Hall', '3420 Holdrege St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LRH', NULL, NULL, NULL, 'LRH', NULL, NULL, 1, NULL, NULL, NULL),
(28980, 'Service Building', '1915 N 37th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SVC', NULL, NULL, NULL, 'SVC', NULL, NULL, 1, NULL, NULL, NULL),
(28981, 'Forestry Hall', '1800 N 37th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FORS', NULL, NULL, NULL, 'FORS', NULL, NULL, 1, NULL, NULL, NULL),
(28982, 'Theodore Jorgensen Hall', '855 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/JH', NULL, NULL, NULL, 'JH', NULL, NULL, 1, NULL, NULL, NULL),
(28983, 'Colonial Terrace Apartment A-1', '3330 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA1', NULL, NULL, NULL, 'CTA1', NULL, NULL, 1, NULL, NULL, NULL),
(28984, 'Colonial Terrace Apartment A-2', '3400 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA2', NULL, NULL, NULL, 'CTA2', NULL, NULL, 1, NULL, NULL, NULL),
(28985, 'Colonial Terrace Apartment A-3', '3323 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA3', NULL, NULL, NULL, 'CTA3', NULL, NULL, 1, NULL, NULL, NULL),
(28986, 'Colonial Terrace Apartment A-4', '3401 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA4', NULL, NULL, NULL, 'CTA4', NULL, NULL, 1, NULL, NULL, NULL),
(28987, 'Colonial Terrace Apartment B', '3344 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTB', NULL, NULL, NULL, 'CTB', NULL, NULL, 1, NULL, NULL, NULL),
(28988, 'Colonial Terrace Apartment C-1', '3320 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC1', NULL, NULL, NULL, 'CTC1', NULL, NULL, 1, NULL, NULL, NULL),
(28989, 'Colonial Terrace Apartment C-2', '3340 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC2', NULL, NULL, NULL, 'CTC2', NULL, NULL, 1, NULL, NULL, NULL),
(28990, 'Colonial Terrace Apartment C-3', '3315 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC3', NULL, NULL, NULL, 'CTC3', NULL, NULL, 1, NULL, NULL, NULL),
(28991, 'Baker Hall', '1830 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MBH', NULL, NULL, NULL, 'MBH', NULL, NULL, 1, NULL, NULL, NULL),
(28992, 'Ruth Staples Laboratory', '1855 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CDL', NULL, NULL, NULL, 'CDL', NULL, NULL, 1, NULL, NULL, NULL),
(28993, 'Warehouse 1', '3630 East Campus Loop N', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/W1', NULL, NULL, NULL, 'W1', NULL, NULL, 1, NULL, NULL, NULL),
(28994, 'VBS Annex', '1900 N 42nd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/VDC', NULL, NULL, NULL, 'VDC', NULL, NULL, 1, NULL, NULL, NULL),
(28995, 'Veterinary Medicine and Biomedical Sciences Hall', '1880 N 42nd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/VBS', NULL, NULL, NULL, 'VBS', NULL, NULL, 1, NULL, NULL, NULL),
(28996, 'Veterinary Clinical Skills Laboratory', '2000 N 43rd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/VCSL', NULL, NULL, NULL, 'VCSL', NULL, NULL, 1, NULL, NULL, NULL),
(28997, 'Abel-Sandoz Welcome Center', '830 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ASWC', NULL, NULL, NULL, 'ASWC', NULL, NULL, 1, NULL, NULL, NULL),
(28998, 'Splinter Laboratories', '2000 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SPL', NULL, NULL, NULL, 'SPL', NULL, NULL, 1, NULL, NULL, NULL),
(28999, 'Sewage Sterilization Plant', '2005 N 43rd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SEW', NULL, NULL, NULL, 'SEW', NULL, NULL, 1, NULL, NULL, NULL),
(29000, 'National Agroforestry Center Storage Building - USDA', '2140 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NAST', NULL, NULL, NULL, 'NAST', NULL, NULL, 1, NULL, NULL, NULL),
(29001, 'Animal Science Complex', '3940 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ANSC', NULL, NULL, NULL, 'ANSC', NULL, NULL, 1, NULL, NULL, NULL),
(29002, 'Landscape Services East Campus', '3520 East Campus Loop N', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LSEC', NULL, NULL, NULL, 'LSEC', NULL, NULL, 1, NULL, NULL, NULL),
(29003, 'Perin Porch', '3621 East Campus Loop S', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PER', NULL, NULL, NULL, 'PER', NULL, NULL, 1, NULL, NULL, NULL),
(29004, 'Agronomy & Horticulture/Forestry Shops', '2103 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHFS', NULL, NULL, NULL, 'AHFS', NULL, NULL, 1, NULL, NULL, NULL),
(29005, 'International Quilt Museum', '1523 N 33rd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/IQM', NULL, NULL, NULL, 'IQM', NULL, NULL, 1, NULL, NULL, NULL),
(29006, 'Community Garden Shed', '4401 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/GRDN', NULL, NULL, NULL, 'GRDN', NULL, NULL, 1, NULL, NULL, NULL),
(29007, 'Colonial Terrace Apartment C-4', '3333 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC4', NULL, NULL, NULL, 'CTC4', NULL, NULL, 1, NULL, NULL, NULL),
(29008, 'Colonial Terrace Apartment D-1', '3301 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTD1', NULL, NULL, NULL, 'CTD1', NULL, NULL, 1, NULL, NULL, NULL),
(29009, 'Colonial Terrace Apartment D-2', '3345 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTD2', NULL, NULL, NULL, 'CTD2', NULL, NULL, 1, NULL, NULL, NULL),
(29010, 'Colonial Terrace Apartments Shop 1', '3332 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTS1', NULL, NULL, NULL, 'CTS1', NULL, NULL, 1, NULL, NULL, NULL),
(29011, 'Colonial Terrace Apartments Shop 2', '3342 Starr St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTS2', NULL, NULL, NULL, 'CTS2', NULL, NULL, 1, NULL, NULL, NULL),
(29012, 'Pershing Maintenance', '2000 N 33rd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PM', NULL, NULL, NULL, 'PM', NULL, NULL, 1, NULL, NULL, NULL),
(29013, 'Agronomy & Horticulture Greenhouse 1', '2100 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG1', NULL, NULL, NULL, 'AHG1', NULL, NULL, 1, NULL, NULL, NULL),
(29014, 'Agronomy & Horticulture Greenhouse 2', '2041 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG2', NULL, NULL, NULL, 'AHG2', NULL, NULL, 1, NULL, NULL, NULL),
(29015, 'Natural Resources Research Annex', '2051 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NRRA', NULL, NULL, NULL, 'NRRA', NULL, NULL, 1, NULL, NULL, NULL),
(29016, 'Architecture Hall West', '400 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/ARCW', NULL, NULL, NULL, 'ARCW', NULL, NULL, 1, NULL, NULL, NULL),
(29017, 'Mueller Tower', '1307 U St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/MUEL', NULL, NULL, NULL, 'MUEL', NULL, NULL, 1, NULL, NULL, NULL),
(29018, 'Selleck Quad Building K', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELK', NULL, NULL, NULL, 'SELK', NULL, NULL, 1, NULL, NULL, NULL),
(29019, 'Stadium West', '1100 T St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STW', NULL, NULL, NULL, 'STW', NULL, NULL, 1, NULL, NULL, NULL),
(29020, 'Abel Hall', '880 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ARH', NULL, NULL, NULL, 'ARH', NULL, NULL, 1, NULL, NULL, NULL),
(29021, 'Sandoz Hall', '820 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SZRH', NULL, NULL, NULL, 'SZRH', NULL, NULL, 1, NULL, NULL, NULL),
(29022, 'Abel-Sandoz Food Service Building', '840 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ASFS', NULL, NULL, NULL, 'ASFS', NULL, NULL, 1, NULL, NULL, NULL),
(29023, 'Campus Recreation Boat House', '1000 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BOAT', NULL, NULL, NULL, 'BOAT', NULL, NULL, 1, NULL, NULL, NULL),
(29024, 'Watson Building', '1309 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WAT', NULL, NULL, NULL, 'WAT', NULL, NULL, 1, NULL, NULL, NULL),
(29025, 'Ice Box', '1880 Transformation Dr', NULL, NULL, 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/ICBX', NULL, NULL, NULL, 'ICBX', NULL, NULL, 1, NULL, NULL, NULL),
(29026, 'Prem S. Paul Research Center at Whittier School', '2200 Vine St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/WHIT', NULL, NULL, NULL, 'WHIT', NULL, NULL, 1, NULL, NULL, NULL),
(29027, '19th and Vine Parking Garage', '1830 Vine St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/19PG', NULL, NULL, NULL, '19PG', NULL, NULL, 1, NULL, NULL, NULL),
(29028, 'Architecture Hall Link', '404 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ARCL', NULL, NULL, NULL, 'ARCL', NULL, NULL, 1, NULL, NULL, NULL),
(29029, 'Husker Hall', '705 N 23rd St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/HUSK', NULL, NULL, NULL, 'HUSK', NULL, NULL, 1, NULL, NULL, NULL),
(29030, 'Bioscience Greenhouses', '1901 Vine St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BIOG', NULL, NULL, NULL, 'BIOG', NULL, NULL, 1, NULL, NULL, NULL),
(29031, 'Facilities Management & Planning', '1901 Y St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMP', NULL, NULL, NULL, 'FMP', NULL, NULL, 1, NULL, NULL, NULL),
(29032, 'U Street Apartments', '2224 U St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/UST', NULL, NULL, NULL, 'UST', NULL, NULL, 1, NULL, NULL, NULL),
(29033, 'Vine Street Apartments West', '2222 Vine St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/2222', NULL, NULL, NULL, '2222', NULL, NULL, 1, NULL, NULL, NULL),
(29034, 'Vine Street Apartments East', '2244 Vine St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/2244', NULL, NULL, NULL, '2244', NULL, NULL, 1, NULL, NULL, NULL),
(29035, 'Landscape Services Metal Canopy', '1340 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LSMC', NULL, NULL, NULL, 'LSMC', NULL, NULL, 1, NULL, NULL, NULL),
(29036, 'Mary Riepma Ross Media Arts Center-Van Brunt Visitors Center', '313 N 13th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RVB', NULL, NULL, NULL, 'RVB', NULL, NULL, 1, NULL, NULL, NULL),
(29037, 'Nebraska Champions Club', '707 Stadium Dr', NULL, NULL, 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/NECH', NULL, NULL, NULL, 'NECH', NULL, NULL, 1, NULL, NULL, NULL),
(29038, 'The Courtyards', '733 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CORT', NULL, NULL, NULL, 'CORT', NULL, NULL, 1, NULL, NULL, NULL),
(29039, 'The Village', '1055 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/VILL', NULL, NULL, NULL, 'VILL', NULL, NULL, 1, NULL, NULL, NULL),
(29040, 'Hawks Championship Center', '1111 Salt Creek Rdwy', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HCC', NULL, NULL, NULL, 'HCC', NULL, NULL, 1, NULL, NULL, NULL),
(29041, 'Selleck Quad Building D', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELD', NULL, NULL, NULL, 'SELD', NULL, NULL, 1, NULL, NULL, NULL),
(29042, 'Selleck Quad Building E', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELE', NULL, NULL, NULL, 'SELE', NULL, NULL, 1, NULL, NULL, NULL),
(29043, 'Selleck Quad Building F', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELF', NULL, NULL, NULL, 'SELF', NULL, NULL, 1, NULL, NULL, NULL),
(29044, 'Selleck Quad Building G', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELG', NULL, NULL, NULL, 'SELG', NULL, NULL, 1, NULL, NULL, NULL),
(29045, 'Selleck Quad Building H', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELH', NULL, NULL, NULL, 'SELH', NULL, NULL, 1, NULL, NULL, NULL),
(29046, 'Selleck Quad Building J', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELJ', NULL, NULL, NULL, 'SELJ', NULL, NULL, 1, NULL, NULL, NULL),
(29047, 'Selleck Quad Building L - Food Service', '600 N 15th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELL', NULL, NULL, NULL, 'SELL', NULL, NULL, 1, NULL, NULL, NULL),
(29048, 'Stadium North', '1100 T St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STNO', NULL, NULL, NULL, 'STNO', NULL, NULL, 1, NULL, NULL, NULL),
(29049, 'Stadium South', '1100 T St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STSO', NULL, NULL, NULL, 'STSO', NULL, NULL, 1, NULL, NULL, NULL),
(29050, 'Harper Hall', '1150 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HRH', NULL, NULL, NULL, 'HRH', NULL, NULL, 1, NULL, NULL, NULL);
INSERT INTO `location` (`id`, `name`, `streetaddress1`, `streetaddress2`, `room`, `city`, `state`, `zip`, `mapurl`, `webpageurl`, `hours`, `directions`, `additionalpublicinfo`, `type`, `phone`, `standard`, `user_id`, `display_order`, `calendar_id`) VALUES
(29051, 'Schramm Hall', '1130 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SCRH', NULL, NULL, NULL, 'SCRH', NULL, NULL, 1, NULL, NULL, NULL),
(29052, '1101 Y', '1101 Y St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/YS1', NULL, NULL, NULL, 'YS1', NULL, NULL, 1, NULL, NULL, NULL),
(29053, 'Smith Hall', '1120 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SMRH', NULL, NULL, NULL, 'SMRH', NULL, NULL, 1, NULL, NULL, NULL),
(29054, 'Harper Dining Center', '1140 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HDC', NULL, NULL, NULL, 'HDC', NULL, NULL, 1, NULL, NULL, NULL),
(29055, '2511 Kimco Court A', '2511 Kimco Ct', NULL, NULL, 'Lincoln', 'NE', '68521', 'https://maps.unl.edu/L014', NULL, NULL, NULL, 'L014', NULL, NULL, 1, NULL, NULL, NULL),
(29056, 'Fleming Fields Park Concessions Bldg', '3233 Huntington Ave', NULL, NULL, 'Lincoln', 'NE', '68504', 'https://maps.unl.edu/FFCB', NULL, NULL, NULL, 'FFCB', NULL, NULL, 1, NULL, NULL, NULL),
(29057, 'Campus Rec Equipment Building 3 - Whittier Fields', '2251 W St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ORB3', NULL, NULL, NULL, 'ORB3', NULL, NULL, 1, NULL, NULL, NULL),
(29058, 'UNL Children\'s Center', '2225 W St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CHC', NULL, NULL, NULL, 'CHC', NULL, NULL, 1, NULL, NULL, NULL),
(29059, 'Bus Garage', '1935 N Antelope Valley Pky', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BUSG', NULL, NULL, NULL, 'BUSG', NULL, NULL, 1, NULL, NULL, NULL),
(29060, 'Landscape Services Equipment Building', '3620 East Campus Loop N', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LSEB', NULL, NULL, NULL, 'LSEB', NULL, NULL, 1, NULL, NULL, NULL),
(29061, 'East Thermal Energy Storage', '3755 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ETES', NULL, NULL, NULL, 'ETES', NULL, NULL, 1, NULL, NULL, NULL),
(29062, 'Fleming Fields Annex Building', '2301 N 33rd St', NULL, NULL, 'Lincoln', 'NE', '68504', 'https://maps.unl.edu/FFAB', NULL, NULL, NULL, 'FFAB', NULL, NULL, 1, NULL, NULL, NULL),
(29063, 'Utility Plant, City Campus', '905 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CCUP', NULL, NULL, NULL, 'CCUP', NULL, NULL, 1, NULL, NULL, NULL),
(29064, 'North Building 2', '1350 Military Rd', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NB2', NULL, NULL, NULL, 'NB2', NULL, NULL, 1, NULL, NULL, NULL),
(29065, 'North Building 1', '1300 Military Rd', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NB1', NULL, NULL, NULL, 'NB1', NULL, NULL, 1, NULL, NULL, NULL),
(29066, 'Recycling and Refuse Building', '1311 Military Rd', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RRB', NULL, NULL, NULL, 'RRB', NULL, NULL, 1, NULL, NULL, NULL),
(29067, 'ITS Annex', '1321 Military Rd', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ITSA', NULL, NULL, NULL, 'ITSA', NULL, NULL, 1, NULL, NULL, NULL),
(29068, '18th & R Parking Garage', '1801 R St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/18R', NULL, NULL, NULL, '18R', NULL, NULL, 1, NULL, NULL, NULL),
(29069, 'Facilities Implement Building', '1330 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/FIB', NULL, NULL, NULL, 'FIB', NULL, NULL, 1, NULL, NULL, NULL),
(29070, 'NEMA Building', '1360 Military Rd', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NEMA', NULL, NULL, NULL, 'NEMA', NULL, NULL, 1, NULL, NULL, NULL),
(29071, 'Documents Facility', '1331 Military Rd', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/DF', NULL, NULL, NULL, 'DF', NULL, NULL, 1, NULL, NULL, NULL),
(29072, 'Alex Gordon Training Complex', 'Line Dr', NULL, NULL, 'Lincoln', 'NE', '', 'https://maps.unl.edu/L045', NULL, NULL, NULL, 'L045', NULL, NULL, 1, NULL, NULL, NULL),
(29073, 'Pinnacle Bank Arena', '400 Pinnacle Arena Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/PBA', NULL, NULL, NULL, 'PBA', NULL, NULL, 1, NULL, NULL, NULL),
(29074, 'Outdoor Adventures Center', '930 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/OAC', NULL, NULL, NULL, 'OAC', NULL, NULL, 1, NULL, NULL, NULL),
(29075, 'Campus Renewable Energy System Building', '2402 Salt Creek Rdwy', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CRES', NULL, NULL, NULL, 'CRES', NULL, NULL, 1, NULL, NULL, NULL),
(29076, 'Recreation and Wellness Center', '1717 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/RWC', NULL, NULL, NULL, 'RWC', NULL, NULL, 1, NULL, NULL, NULL),
(29077, 'Eastside Suites', '433 N 19th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/ESST', NULL, NULL, NULL, 'ESST', NULL, NULL, 1, NULL, NULL, NULL),
(29078, 'Sid and Hazel Dillon Tennis Center', '2400 N Antelope Valley Pky', NULL, NULL, 'Lincoln', 'NE', '68521', 'https://maps.unl.edu/DTC', NULL, NULL, NULL, 'DTC', NULL, NULL, 1, NULL, NULL, NULL),
(29079, 'Maintenance Storage Building', '1910 N Antelope Valley Pky', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MSB', NULL, NULL, NULL, 'MSB', NULL, NULL, 1, NULL, NULL, NULL),
(29080, 'Breslow Ice Center', '433 V St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BIC', NULL, NULL, NULL, 'BIC', NULL, NULL, 1, NULL, NULL, NULL),
(29081, 'Material Handling Facility', '3700 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MHF', NULL, NULL, NULL, 'MHF', NULL, NULL, 1, NULL, NULL, NULL),
(29082, 'Howard L. Hawks Hall', '730 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HLH', NULL, NULL, NULL, 'HLH', NULL, NULL, 1, NULL, NULL, NULL),
(29083, 'Mabel Lee Fields IPC', '1433 W St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MIPC', NULL, NULL, NULL, 'MIPC', NULL, NULL, 1, NULL, NULL, NULL),
(29084, 'Campus Rec Equipment Building 7 - Mabel Lee Fields', '1433 W St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ORB7', NULL, NULL, NULL, 'ORB7', NULL, NULL, 1, NULL, NULL, NULL),
(29085, 'Greenhouse Innovation Center', '1920 N 21st St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ICG', NULL, NULL, NULL, 'ICG', NULL, NULL, 1, NULL, NULL, NULL),
(29086, 'Food Innovation Center', '1901 N 21st St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/FIC', NULL, NULL, NULL, 'FIC', NULL, NULL, 1, NULL, NULL, NULL),
(29087, 'Innovation Commons Conference Center', '2021 Transformation Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ICC', NULL, NULL, NULL, 'ICC', NULL, NULL, 1, NULL, NULL, NULL),
(29088, 'Fluid Cooler Building', '520 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/FCB', NULL, NULL, NULL, 'FCB', NULL, NULL, 1, NULL, NULL, NULL),
(29089, 'Nebraska Veterinary Diagnostic Center', '4040 East Campus Loop N', NULL, NULL, 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/NVDC', NULL, NULL, NULL, 'NVDC', NULL, NULL, 1, NULL, NULL, NULL),
(29090, 'Landscape Implement Building', '1320 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LIB', NULL, NULL, NULL, 'LIB', NULL, NULL, 1, NULL, NULL, NULL),
(29091, 'Willa S. Cather Dining Complex', '530 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/WCDC', NULL, NULL, NULL, 'WCDC', NULL, NULL, 1, NULL, NULL, NULL),
(29092, '1217 Q St', '1217 Q St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/L055', NULL, NULL, NULL, 'L055', NULL, NULL, 1, NULL, NULL, NULL),
(29093, 'Utility Response Facility', '3730 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/UTF', NULL, NULL, NULL, 'UTF', NULL, NULL, 1, NULL, NULL, NULL),
(29094, 'City Thermal Energy Storage', '1340 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CTES', NULL, NULL, NULL, 'CTES', NULL, NULL, 1, NULL, NULL, NULL),
(29095, 'University Health Center and College of Nursing', '550 N 19th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/HCCN', NULL, NULL, NULL, 'HCCN', NULL, NULL, 1, NULL, NULL, NULL),
(29096, 'Library Depository Retrieval Facility', '2055 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LDR', NULL, NULL, NULL, 'LDR', NULL, NULL, 1, NULL, NULL, NULL),
(29097, 'Massengale Residential Center', '1710 Arbor Dr', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MRC', NULL, NULL, NULL, 'MRC', NULL, NULL, 1, NULL, NULL, NULL),
(29098, 'Orchard House Replacement', '4417 Fair St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/OHR', NULL, NULL, NULL, 'OHR', NULL, NULL, 1, NULL, NULL, NULL),
(29099, '18th & S Support Building', '510 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/18S', NULL, NULL, NULL, '18S', NULL, NULL, 1, NULL, NULL, NULL),
(29100, 'The Robert E. Knoll Residential Center', '440 N 17th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KNOL', NULL, NULL, NULL, 'KNOL', NULL, NULL, 1, NULL, NULL, NULL),
(29101, 'Agronomy & Horticulture Greenhouse 3', '2065 N 38th St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG3', NULL, NULL, NULL, 'AHG3', NULL, NULL, 1, NULL, NULL, NULL),
(29102, 'Johnny Carson Center for Emerging Media Arts', '1300 Q St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CEMA', NULL, NULL, NULL, 'CEMA', NULL, NULL, 1, NULL, NULL, NULL),
(29103, 'USDA Physiology Building - USDA', '3708 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/USDP', NULL, NULL, NULL, 'USDP', NULL, NULL, 1, NULL, NULL, NULL),
(29104, 'Materials Management Facility', '3735 Merrill St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MMF', NULL, NULL, NULL, 'MMF', NULL, NULL, 1, NULL, NULL, NULL),
(29105, 'Facilities Management G', '1901 Y St', NULL, NULL, 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMG', NULL, NULL, NULL, 'FMG', NULL, NULL, 1, NULL, NULL, NULL),
(29106, 'Engineering Research Center', '880 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ERC', NULL, NULL, NULL, 'ERC', NULL, NULL, 1, NULL, NULL, NULL),
(29107, 'Carolyn Pope Edwards Hall', '840 N 14th St', NULL, NULL, 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/CPEH', NULL, NULL, NULL, 'CPEH', NULL, NULL, 1, NULL, NULL, NULL),
(29108, 'The Rise Building', '2125 Transformation Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RISE', NULL, NULL, NULL, 'RISE', NULL, NULL, 1, NULL, NULL, NULL),
(29109, 'The Scarlet Hotel', '2101 Transformation Dr', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SCAR', NULL, NULL, NULL, 'SCAR', NULL, NULL, 1, NULL, NULL, NULL),
(29110, 'Gwendolyn A. Newkirk Human Sciences Building', '1650 N 35th St', NULL, NULL, 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/GNHS', NULL, NULL, NULL, 'GNHS', NULL, NULL, 1, NULL, NULL, NULL),
(29111, 'Neihardt Center', '540 N 16th St', NULL, NULL, 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NRC ', NULL, NULL, NULL, 'NRC', NULL, NULL, 1, NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `performer`
--

DROP TABLE IF EXISTS `performer`;
CREATE TABLE `performer` (
  `id` int(10) UNSIGNED NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `personalname` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `jobtitle` varchar(100) DEFAULT NULL,
  `organizationname` varchar(100) DEFAULT NULL,
  `personalwebpageurl` longtext,
  `organizationwebpageurl` longtext,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`id`, `name`, `description`, `standard`) VALUES
(2, 'Event Delete', 'Event: Delete', 1),
(3, 'Event Post', 'Event: Move To Upcoming', 1),
(4, 'Event Send Event to Pending Queue', 'Event: Move to Pending', 1),
(5, 'Event Edit', 'Event: Edit', 1),
(6, 'Event Recommend', 'Event: Recommend', 1),
(7, 'Event Feature', 'Event: Feature', 1),
(16, 'Calendar Delete', 'Calendar: Delete', 0),
(18, 'Calendar Change User Permissions', 'Calendar: Edit User Permissions', 0),
(19, 'Calendar Edit', 'Calendar: Edit', 0),
(22, 'Calendar Edit Subscription', 'Calendar: Edit Subscriptions', 0),
(25, 'Event Create', 'Event: Create', 1);

-- --------------------------------------------------------

--
-- Table structure for table `publiccontact`
--

DROP TABLE IF EXISTS `publiccontact`;
CREATE TABLE `publiccontact` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
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
  `webpageurl` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recurringdate`
--

DROP TABLE IF EXISTS `recurringdate`;
CREATE TABLE `recurringdate` (
  `id` int(10) UNSIGNED NOT NULL,
  `recurringdate` date NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `recurrence_id` int(10) UNSIGNED NOT NULL,
  `ongoing` tinyint(1) DEFAULT NULL,
  `unlinked` tinyint(1) DEFAULT '0',
  `event_datetime_id` int(10) DEFAULT NULL,
  `canceled` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `relatedevent`
--

DROP TABLE IF EXISTS `relatedevent`;
CREATE TABLE `relatedevent` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `related_event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `relationtype` varchar(100) NOT NULL DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT ' ',
  `standard` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `user_uid` varchar(255) NOT NULL DEFAULT ' ',
  `lastaction` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sponsor`
--

DROP TABLE IF EXISTS `sponsor`;
CREATE TABLE `sponsor` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1',
  `sponsortype` varchar(255) DEFAULT NULL,
  `webpageurl` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
CREATE TABLE `subscription` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `automaticapproval` tinyint(1) NOT NULL DEFAULT '0',
  `timeperiod` date DEFAULT NULL,
  `expirationdate` date DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_has_calendar`
--

DROP TABLE IF EXISTS `subscription_has_calendar`;
CREATE TABLE `subscription_has_calendar` (
  `id` int(11) UNSIGNED NOT NULL,
  `subscription_id` int(11) UNSIGNED NOT NULL,
  `calendar_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` varchar(100) NOT NULL DEFAULT ' ',
  `account_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `accountstatus` varchar(100) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  `uidcreated` varchar(100) DEFAULT NULL,
  `datelastupdated` datetime DEFAULT NULL,
  `uidlastupdated` varchar(100) DEFAULT NULL,
  `token` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_has_permission`
--

DROP TABLE IF EXISTS `user_has_permission`;
CREATE TABLE `user_has_permission` (
  `permission_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_uid` varchar(100) NOT NULL DEFAULT ' ',
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webcast`
--

DROP TABLE IF EXISTS `webcast`;
CREATE TABLE `webcast` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `url` longtext NOT NULL,
  `additionalinfo` longtext,
  `user_id` varchar(100) DEFAULT NULL,
  `calendar_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admissioncharge`
--
ALTER TABLE `admissioncharge`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admissioninfo`
--
ALTER TABLE `admissioninfo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id_idx` (`event_id`);

--
-- Indexes for table `attendancerestriction`
--
ALTER TABLE `attendancerestriction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendancerestriction_event_id_idx` (`event_id`);

--
-- Indexes for table `audience`
--
ALTER TABLE `audience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id_idx` (`account_id`),
  ADD KEY `shortname_idx` (`shortname`);

--
-- Indexes for table `calendar_has_event`
--
ALTER TABLE `calendar_has_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `che_calendar_id_idx` (`calendar_id`),
  ADD KEY `che_event_id_idx` (`event_id`),
  ADD KEY `che_status_idx` (`status`),
  ADD KEY `che_event_calendar_status` (`calendar_id`,`status`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_title_idx` (`title`),
  ADD KEY `approvedforcirculation` (`approvedforcirculation`);

--
-- Indexes for table `eventdatetime`
--
ALTER TABLE `eventdatetime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `edt_event_id_idx` (`event_id`),
  ADD KEY `edt_location_id_idx` (`location_id`),
  ADD KEY `edt_starttime_idx` (`starttime`),
  ADD KEY `edt_endtime_idx` (`endtime`),
  ADD KEY `edt_starttime_recurringtype` (`starttime`,`recurringtype`,`id`);

--
-- Indexes for table `eventtype`
--
ALTER TABLE `eventtype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eventtype_name_idx` (`name`);

--
-- Indexes for table `event_has_eventtype`
--
ALTER TABLE `event_has_eventtype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ehe_event_id_idx` (`event_id`),
  ADD KEY `ehe_eventtype_id_idx` (`eventtype_id`);

--
-- Indexes for table `event_has_keyword`
--
ALTER TABLE `event_has_keyword`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ehk_event_id_idx` (`event_id`),
  ADD KEY `ehk_sponsor_id_idx` (`keyword_id`);

--
-- Indexes for table `event_has_sponsor`
--
ALTER TABLE `event_has_sponsor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ehs_event_id_idx` (`event_id`),
  ADD KEY `ehs_sponsor_id_idx` (`sponsor_id`);

--
-- Indexes for table `event_isopento_audience`
--
ALTER TABLE `event_isopento_audience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eia_event_id_idx` (`event_id`),
  ADD KEY `eia_audience_id_idx` (`audience_id`);

--
-- Indexes for table `event_targets_audience`
--
ALTER TABLE `event_targets_audience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eta_event_id_idx` (`event_id`),
  ADD KEY `eta_audience_id_idx` (`audience_id`);

--
-- Indexes for table `facebook_accounts`
--
ALTER TABLE `facebook_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keyword`
--
ALTER TABLE `keyword`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_name_idx` (`name`);

--
-- Indexes for table `performer`
--
ALTER TABLE `performer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publiccontact`
--
ALTER TABLE `publiccontact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id_idx` (`event_id`);

--
-- Indexes for table `recurringdate`
--
ALTER TABLE `recurringdate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `unlinked` (`unlinked`),
  ADD KEY `event_id-recurringdate-unlinked` (`event_id`,`recurringdate`,`unlinked`),
  ADD KEY `event_datetime_id` (`event_datetime_id`),
  ADD KEY `going_lookup_idx` (`event_datetime_id`,`ongoing`,`recurrence_id`),
  ADD KEY `event_date_lookup_idx` (`event_id`,`recurringdate`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`user_uid`);

--
-- Indexes for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendar_id_idx` (`calendar_id`);

--
-- Indexes for table `subscription_has_calendar`
--
ALTER TABLE `subscription_has_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `user_has_permission`
--
ALTER TABLE `user_has_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendar_id` (`calendar_id`),
  ADD KEY `user_uid` (`user_uid`);

--
-- Indexes for table `webcast`
--
ALTER TABLE `webcast`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admissioncharge`
--
ALTER TABLE `admissioncharge`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admissioninfo`
--
ALTER TABLE `admissioninfo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendancerestriction`
--
ALTER TABLE `attendancerestriction`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audience`
--
ALTER TABLE `audience`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calendar_has_event`
--
ALTER TABLE `calendar_has_event`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventdatetime`
--
ALTER TABLE `eventdatetime`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventtype`
--
ALTER TABLE `eventtype`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `event_has_eventtype`
--
ALTER TABLE `event_has_eventtype`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_has_keyword`
--
ALTER TABLE `event_has_keyword`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_has_sponsor`
--
ALTER TABLE `event_has_sponsor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_isopento_audience`
--
ALTER TABLE `event_isopento_audience`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_targets_audience`
--
ALTER TABLE `event_targets_audience`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facebook_accounts`
--
ALTER TABLE `facebook_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keyword`
--
ALTER TABLE `keyword`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performer`
--
ALTER TABLE `performer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `publiccontact`
--
ALTER TABLE `publiccontact`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recurringdate`
--
ALTER TABLE `recurringdate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsor`
--
ALTER TABLE `sponsor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_has_calendar`
--
ALTER TABLE `subscription_has_calendar`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_has_permission`
--
ALTER TABLE `user_has_permission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webcast`
--
ALTER TABLE `webcast`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
