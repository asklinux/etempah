-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Apr 04, 2013 at 02:55 PM
-- Server version: 5.1.67
-- PHP Version: 5.3.6-13ubuntu3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stbmci`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL DEFAULT '0',
  `booking_ref_no` varchar(16) NOT NULL DEFAULT '',
  `booking_type` int(1) NOT NULL DEFAULT '0',
  `full_day` tinyint(1) NOT NULL DEFAULT '0',
  `fk_booking_id` int(11) NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `booking_status` int(1) NOT NULL DEFAULT '0',
  `booking_status_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_equipments`
--

CREATE TABLE IF NOT EXISTS `booking_equipments` (
  `booking_equipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `secretariat` varchar(64) COLLATE utf8_bin NOT NULL,
  `booking_with_room` tinyint(1) NOT NULL,
  `equipment_purpose` varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  `place` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `require_technical_person` int(4) NOT NULL DEFAULT '0',
  `booking_equipment_description` text COLLATE utf8_bin NOT NULL,
  `equipment_list` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`booking_equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_foods`
--

CREATE TABLE IF NOT EXISTS `booking_foods` (
  `booking_food_id` int(11) NOT NULL AUTO_INCREMENT,
  `secretariat` int(11) NOT NULL,
  `food_type_ids` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `place` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `food_purpose` varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  `total_pack` int(2) NOT NULL DEFAULT '0',
  `booking_food_description` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`booking_food_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_rooms`
--

CREATE TABLE IF NOT EXISTS `booking_rooms` (
  `booking_room_id` int(11) NOT NULL AUTO_INCREMENT,
  `secretariat` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `chairman` varchar(64) COLLATE utf8_bin NOT NULL,
  `room_purpose` varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  `total_from_agensi` int(4) NOT NULL DEFAULT '0',
  `total_from_nonagensi` int(4) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_bin NOT NULL,
  `fk_room_id` int(4) NOT NULL DEFAULT '0',
  `fk_booking_equipment_id` int(4) NOT NULL DEFAULT '0',
  `fk_booking_food_id` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`booking_room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_transports`
--

CREATE TABLE IF NOT EXISTS `booking_transports` (
  `booking_transport_id` int(11) NOT NULL AUTO_INCREMENT,
  `secretariat` varchar(64) COLLATE utf8_bin NOT NULL,
  `destination_from` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `destination_to` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transport_purpose` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `total_passenger` int(2) NOT NULL DEFAULT '0',
  `trip_type` int(1) NOT NULL DEFAULT '0',
  `transports` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `booking_transport_description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`booking_transport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE IF NOT EXISTS `destinations` (
  `destination_name` varchar(128) DEFAULT NULL,
  UNIQUE KEY `destination_name` (`destination_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_email` varchar(128) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `alt_message` text,
  `max_attempts` int(11) NOT NULL DEFAULT '3',
  `attempts` int(11) NOT NULL DEFAULT '0',
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_attempt` datetime DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE IF NOT EXISTS `equipments` (
  `equipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `quantity` int(5) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_description` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(40) COLLATE utf8_bin NOT NULL,
  `login` varchar(50) COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL DEFAULT '0',
  `activity` varchar(255) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `location` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `building` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `floor` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `capacity` int(11) NOT NULL DEFAULT '0',
  `facilities` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_description` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE IF NOT EXISTS `transports` (
  `transport_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transport_type` int(2) NOT NULL DEFAULT '0',
  `capacity` int(5) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_description` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`transport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transport_types`
--

CREATE TABLE IF NOT EXISTS `transport_types` (
  `transport_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_type_name` varchar(32) NOT NULL,
  `transport_type_cargo` varchar(10) NOT NULL,
  PRIMARY KEY (`transport_type_id`),
  UNIQUE KEY `transport_type_name` (`transport_type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `user_level` tinyint(1) NOT NULL DEFAULT '0',
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  `new_password_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=62 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `user_level`, `activated`, `banned`, `ban_reason`, `deleted`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES
(1, 'admin', '$2a$08$GVxxH9Z3QQqMR.axz3FDzuVYfOtlz10LSEfP9fGfr.j17zSY6h2.u', 'email here', 1, 1, 0, NULL, 0, NULL, NULL, NULL, '65ffb46c8b3bb4f580cbaf660c14ecf2', '127.0.0.1', '2013-04-04 10:03:22', '2012-11-12 12:05:14', '2013-04-04 02:03:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_autologin`
--

CREATE TABLE IF NOT EXISTS `user_autologin` (
  `key_id` char(32) COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL DEFAULT '0',
  `full_name` varchar(64) COLLATE utf8_bin DEFAULT '',
  `position_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `position_level` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `department_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `contact_office` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `contact_office_ext` varchar(4) COLLATE utf8_bin NOT NULL,
  `contact_mobile` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=56 ;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `fk_user_id`, `full_name`, `position_name`, `position_level`, `department_name`, `contact_office`, `contact_office_ext`, `contact_mobile`) VALUES
(1, 1, 'Nama Penuh', 'Jawatan', 'Pangkat', 'Bahagian', 'Telefon Pejabat', 'Sambungan', 'Telefon mudah alih');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;