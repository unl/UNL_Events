# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.24)
# Database: events
# Generation Time: 2015-06-22 17:00:26 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `standard` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;

INSERT INTO `permission` (`id`, `name`, `description`, `standard`)
VALUES
	(2,'Event Delete','Event: Delete',1),
	(3,'Event Post','Event: Move To Upcoming',1),
	(4,'Event Send Event to Pending Queue','Event: Move to Pending',1),
	(5,'Event Edit','Event: Edit',1),
	(6,'Event Recommend','Event: Recommend',1),
	(16,'Calendar Delete','Calendar: Delete',0),
	(18,'Calendar Change User Permissions','Calendar: Edit User Permissions',0),
	(19,'Calendar Edit','Calendar: Edit',0),
	(22,'Calendar Edit Subscription','Calendar: Edit Subscriptions',0),
	(25,'Event Create','Event: Create',1);

/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

# add Create Event permission to all users/calendars that have any permissions
INSERT INTO user_has_permission (user_uid, calendar_id, permission_id)
SELECT DISTINCT user_uid, calendar_id, 25 FROM user_has_permission;

# remove all unused permissions
DELETE FROM user_has_permission WHERE permission_id NOT IN (SELECT id FROM permission);

