-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 29, 2021 at 04:50 PM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 7.1.33

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

-- --------------------------------------------------------

--
-- Table structure for table `admissioncharge`
--

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

CREATE TABLE `attendancerestriction` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `audience`
--

CREATE TABLE `audience` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `calendar_has_event`
--

CREATE TABLE `calendar_has_event` (
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('posted','pending','archived') DEFAULT NULL,
  `source` enum('checked consider event','create event form','recommended','search','subscription','create event api') DEFAULT NULL,
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

CREATE TABLE `eventdatetime` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `location_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `timezone` varchar(30) NOT NULL DEFAULT 'America/Chicago',
  `room` varchar(255) DEFAULT NULL,
  `hours` varchar(255) DEFAULT NULL,
  `directions` longtext,
  `additionalpublicinfo` longtext,
  `recurringtype` varchar(255) NOT NULL DEFAULT 'none',
  `recurs_until` datetime DEFAULT NULL,
  `rectypemonth` varchar(255) DEFAULT NULL,
  `canceled` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventtype`
--

CREATE TABLE `eventtype` (
  `id` int(10) UNSIGNED NOT NULL,
  `calendar_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT ' ',
  `description` varchar(255) DEFAULT NULL,
  `eventtypegroup` varchar(8) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_eventtype`
--

CREATE TABLE `event_has_eventtype` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `eventtype_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_keyword`
--

CREATE TABLE `event_has_keyword` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `keyword_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_sponsor`
--

CREATE TABLE `event_has_sponsor` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sponsor_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_isopento_audience`
--

CREATE TABLE `event_isopento_audience` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `audience_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_targets_audience`
--

CREATE TABLE `event_targets_audience` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `audience_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facebook`
--

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

CREATE TABLE `keyword` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

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
  `display_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `performer`
--

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

CREATE TABLE `permission` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `publiccontact`
--

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

CREATE TABLE `relatedevent` (
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `related_event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `relationtype` varchar(100) NOT NULL DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT ' ',
  `standard` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `user_uid` varchar(255) NOT NULL DEFAULT ' ',
  `lastaction` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sponsor`
--

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

CREATE TABLE `subscription_has_calendar` (
  `id` int(11) UNSIGNED NOT NULL,
  `subscription_id` int(11) UNSIGNED NOT NULL,
  `calendar_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

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

CREATE TABLE `webcast` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `dateavailable` datetime DEFAULT NULL,
  `playertype` varchar(100) DEFAULT NULL,
  `bandwidth` varchar(255) DEFAULT NULL,
  `additionalinfo` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webcastlink`
--

CREATE TABLE `webcastlink` (
  `id` int(10) UNSIGNED NOT NULL,
  `webcast_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `url` longtext,
  `sequencenumber` int(10) UNSIGNED DEFAULT NULL,
  `related` varchar(1) DEFAULT 'n'
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id_idx` (`event_id`);

--
-- Indexes for table `webcastlink`
--
ALTER TABLE `webcastlink`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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

--
-- AUTO_INCREMENT for table `webcastlink`
--
ALTER TABLE `webcastlink`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
